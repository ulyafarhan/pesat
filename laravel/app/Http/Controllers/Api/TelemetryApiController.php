<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Events\NewDetectionTriggered;
use App\Http\Controllers\Controller;
use App\Models\DetectionLabel;
use App\Models\DetectionLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class TelemetryApiController extends Controller
{
    /**
     * Kirim Log Telemetri Deteksi
     *
     * Endpoint untuk menerima data hasil deteksi anomali dari edge device (Python pipeline).
     * Setiap log yang diterima akan disimpan ke database, label akan di-normalisasi via firstOrCreate,
     * dan event broadcast akan dipicu untuk realtime update di dashboard.
     *
     * @group Telemetri AI
     *
     * @authenticated
     *
     * @bodyParam camera_id string required ID kamera terdaftar. Example: CAM-001
     * @bodyParam label_detected string required Label hasil deteksi AI. Example: [Wanita] R-PKN-001: Tidak mengenakan hijab
     * @bodyParam confidence_score number required Skor kepercayaan deteksi (0.0 - 1.0). Example: 0.92
     *
     * @response 201 scenario="Sukses" {"status":"success","data":{"id":1,"camera_id":"CAM-001","label_id":1,"confidence_score":"0.920","created_at":"2026-06-19T10:00:00.000000Z","label_detected":"[Wanita] R-PKN-001: Tidak mengenakan hijab","camera":{"id":"CAM-001","location_name":"Taman Riyadhah","latitude":"5.18020000","longitude":"97.15070000","is_active":true},"label":{"id":1,"name":"[Wanita] R-PKN-001: Tidak mengenakan hijab"}}}
     * @response 401 scenario="Unauthorized" {"status":"error","message":"Unauthorized"}
     * @response 422 scenario="Validasi Gagal" {"message":"The camera id field is required.","errors":{"camera_id":["The camera id field is required."]}}
     */
    public function store(Request $request): JsonResponse
    {
        $apiKey = config('services.pesat.api_key');
        if ($apiKey) {
            $token = $request->bearerToken();
            if (! $token || $token !== $apiKey) {
                return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
            }
        }

        $validated = $request->validate([
            'camera_id' => 'required|string|exists:cameras,id',
            'label_detected' => 'required|string',
            'confidence_score' => 'required|numeric|between:0,1',
            'violation_category' => 'nullable|string|max:50',
            'snapshot' => 'nullable|string|max:100',
        ]);

        $labelId = Cache::remember("detection_label_id_{$validated['label_detected']}", 300, function () use ($validated) {
            $label = DetectionLabel::firstOrCreate([
                'name' => $validated['label_detected'],
            ]);
            return $label->id;
        });

        $log = DetectionLog::create([
            'camera_id' => $validated['camera_id'],
            'label_id' => $labelId,
            'confidence_score' => $validated['confidence_score'],
            'violation_category' => $validated['violation_category'] ?? null,
            'snapshot' => $validated['snapshot'] ?? null,
            'created_at' => \Carbon\Carbon::now(),
        ]);

        try {
            broadcast(new NewDetectionTriggered($log));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("Gagal memicu broadcast telemetri: " . $e->getMessage());
        }

        return response()->json([
            'status' => 'success',
            'data' => $log->load(['camera', 'label']),
        ], 201);
    }

    /**
     * Ambil Log Deteksi Terbaru (Polling)
     *
     * Endpoint polling ringan untuk mendapatkan log deteksi terbaru.
     * Gunakan parameter `after_id` untuk incremental polling (hanya ambil data baru setelah ID tertentu).
     * Gunakan parameter `camera_id` untuk filter per kamera.
     *
     * @group Telemetri AI
     *
     * @unauthenticated
     *
     * @queryParam after_id integer ID log terakhir yang sudah diterima client. Hanya log dengan ID lebih besar akan dikembalikan. Example: 150
     * @queryParam camera_id string Filter berdasarkan ID kamera. Example: CAM-001
     * @queryParam limit integer Jumlah maksimal log yang dikembalikan (default: 30, max: 100). Example: 30
     *
     * @response 200 scenario="Sukses" {"status":"success","data":[{"id":151,"camera_id":"CAM-001","label_id":1,"confidence_score":"0.920","created_at":"2026-06-19T10:00:00.000000Z","label_detected":"[Wanita] R-PKN-001","camera":{"id":"CAM-001","location_name":"Taman Riyadhah"},"label":{"id":1,"name":"[Wanita] R-PKN-001"}}],"meta":{"total_today":42,"latest_id":151}}
     */
    public function latest(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'after_id' => 'nullable|integer|min:0',
            'camera_id' => 'nullable|string|exists:cameras,id',
            'limit' => 'nullable|integer|min:1|max:100',
        ]);

        $limit = $validated['limit'] ?? 30;

        $query = DetectionLog::with(['camera', 'label'])
            ->orderByDesc('id');

        if (! empty($validated['after_id'])) {
            $query->where('id', '>', $validated['after_id']);
        }

        if (! empty($validated['camera_id'])) {
            $query->where('camera_id', $validated['camera_id']);
        }

        $logs = $query->take($limit)->get();

        $totalToday = Cache::remember('detection_total_today', 60, function () {
            return DetectionLog::today()->count();
        });

        return response()->json([
            'status' => 'success',
            'data' => $logs,
            'meta' => [
                'total_today' => $totalToday,
                'latest_id' => $logs->first()?->id,
            ],
        ]);
    }
}
