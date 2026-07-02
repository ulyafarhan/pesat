<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Events\CitizenReportStatusUpdated;
use App\Events\NewCitizenReportSubmitted;
use App\Models\AdminSetting;
use App\Models\CitizenReport;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CitizenReportController extends Controller
{
    /**
     * Kirim Laporan Warga / AI Detection Report
     *
     * Endpoint untuk menerima laporan dari sistem AI edge device atau warga.
     * Sistem otomatis mendeteksi mode jam istirahat (break mode) dan menandai laporan sebagai prioritas.
     * Jika laporan pending dengan lokasi yang sama sudah ada, akan di-update (upsert).
     *
     * @group Laporan Warga
     *
     * @unauthenticated
     *
     * @bodyParam location_name string required Nama lokasi kejadian. Example: Taman Riyadhah - [Wanita] R-PKN-001: Tidak mengenakan hijab
     * @bodyParam latitude number Koordinat latitude GPS. Example: 5.1802
     * @bodyParam longitude number Koordinat longitude GPS. Example: 97.1507
     * @bodyParam media file Bukti media (jpg/png/mp4/mov/avi, max 20MB).
     *
     * @response 201 scenario="Sukses" {"status":"success","data":{"id":"01J5X...","location_name":"Taman Riyadhah","status":"pending","is_break_dispatch":false}}
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'location_name' => 'required|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'media' => 'nullable|file|mimes:jpg,jpeg,png,mp4,mov,avi|max:20480',
            'source' => 'nullable|string|in:public,ai_detection',
            'violation_category' => 'nullable|string|max:50',
        ]);

        $isBreakMode = $this->resolveBreakMode();

        $mediaPath = null;
        if ($request->hasFile('media')) {
            $mediaPath = $request->file('media')->store('citizen_reports', 'public');
        }

        $source = $request->input('source', 'public');
        $violationCategory = $request->input('violation_category');
        $locationName = $request->location_name;
        $parts = explode(' - ', $locationName, 2);
        $baseLocation = $parts[0];

        $report = CitizenReport::pending()
            ->byLocation($baseLocation)
            ->first();

        if ($report) {
            if ($report->media_path) {
                Storage::disk('public')->delete($report->media_path);
            }

            $report->update([
                'location_name' => $locationName,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'reported_at' => Carbon::now(),
                'media_path' => $mediaPath,
                'is_break_dispatch' => $isBreakMode,
                'source' => $source,
                'violation_category' => $violationCategory,
            ]);
        } else {
            $report = CitizenReport::create([
                'location_name' => $locationName,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'reported_at' => Carbon::now(),
                'media_path' => $mediaPath,
                'is_break_dispatch' => $isBreakMode,
                'source' => $source,
                'violation_category' => $violationCategory,
                'status' => 'pending',
            ]);
        }

        try {
            broadcast(new NewCitizenReportSubmitted($report->load('verifier')));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("Gagal memicu broadcast laporan: " . $e->getMessage());
        }

        return response()->json([
            'status' => 'success',
            'data' => $report,
        ], 201);
    }

    /**
     * Ambil Laporan Pending untuk WH Officer
     *
     * Mengembalikan semua laporan warga dengan status pending, diurutkan dari terbaru.
     *
     * @group Laporan Warga
     *
     * @unauthenticated
     *
     * @response 200 scenario="Sukses" {"status":"success","data":[{"id":"01J5X...","location_name":"Taman Riyadhah","status":"pending"}]}
     */
    public function getPendingWH(): JsonResponse
    {
        $reports = CitizenReport::pending()
            ->with('verifier')
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $reports,
        ]);
    }

    /**
     * Ambil Laporan Terbaru (Polling)
     *
     * Endpoint polling untuk mendapatkan laporan terbaru secara incremental.
     *
     * @group Laporan Warga
     *
     * @unauthenticated
     *
     * @queryParam after_id string ULID laporan terakhir. Hanya laporan lebih baru akan dikembalikan. Example: 01J5XABC123
     * @queryParam status string Filter status: pending, verified, rejected. Example: pending
     *
     * @response 200 scenario="Sukses" {"status":"success","data":{"pending":[],"history":[]}}
     */
    public function latest(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'after_id' => 'nullable|string',
            'status' => 'nullable|string|in:pending,verified,rejected',
        ]);

        $pendingQuery = CitizenReport::pending()
            ->with('verifier')
            ->orderByDesc('created_at');

        $historyQuery = CitizenReport::whereIn('status', ['verified', 'rejected'])
            ->with('verifier')
            ->orderByDesc('updated_at')
            ->take(20);

        if (! empty($validated['after_id'])) {
            $pendingQuery->where('id', '>', $validated['after_id']);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'pending' => $pendingQuery->get(),
                'history' => $historyQuery->get(),
            ],
        ]);
    }

    /**
     * Verifikasi Laporan Warga
     *
     * WH Officer memverifikasi (menerima/menolak) laporan warga.
     *
     * @group Laporan Warga
     *
     * @unauthenticated
     *
     * @urlParam id string required ULID laporan. Example: 01J5XABC123DEF456
     *
     * @bodyParam status string required Status verifikasi: verified atau rejected. Example: verified
     * @bodyParam verification_notes string Catatan tindakan lapangan. Example: Tindakan selesai dilakukan di lapangan.
     *
     * @response 200 scenario="Sukses" {"status":"success","data":{"id":"01J5X...","status":"verified","verified_by":2}}
     */
    public function verifyReport(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:verified,rejected',
            'verification_notes' => 'nullable|string',
        ]);

        $report = CitizenReport::findOrFail($id);
        $userId = auth()->id() ?? 2;

        $report->update([
            'status' => $request->status,
            'verified_by' => $userId,
            'verification_notes' => $request->verification_notes,
        ]);

        try {
            broadcast(new CitizenReportStatusUpdated($report->load('verifier')));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("Gagal memicu broadcast status laporan: " . $e->getMessage());
        }

        return response()->json([
            'status' => 'success',
            'data' => $report,
        ]);
    }

    private function resolveBreakMode(): bool
    {
        $settings = AdminSetting::whereIn('key', [
            'break_mode_active',
            'break_start_time',
            'break_end_time',
        ])->get()->keyBy('key');

        $manualActive = $settings->get('break_mode_active');
        if ($manualActive && $manualActive->value === 'true') {
            return true;
        }

        $startSetting = $settings->get('break_start_time');
        $endSetting = $settings->get('break_end_time');
        if ($startSetting && $endSetting) {
            $now = Carbon::now();
            $start = Carbon::createFromTimeString($startSetting->value);
            $end = Carbon::createFromTimeString($endSetting->value);
            if ($now->between($start, $end)) {
                return true;
            }
        }

        return false;
    }
}
