<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\AdminSetting;
use App\Models\Camera;
use App\Models\CitizenReport;
use App\Models\DetectionLog;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $initialLogs = DetectionLog::with(['camera', 'label'])
            ->recent(30)
            ->get();

        $totalToday = DetectionLog::today()->count();
        $cameras = Camera::active()->get();
        $edgeDeviceIds = Camera::active()
            ->whereNotNull('edge_device_id')
            ->distinct()
            ->pluck('edge_device_id');

        return Inertia::render('Dashboard', [
            'initialLogs' => $initialLogs,
            'totalToday' => $totalToday,
            'cameras' => $cameras,
            'edgeDeviceIds' => $edgeDeviceIds,
        ]);
    }

    public function detections(): Response
    {
        $initialLogs = DetectionLog::with(['camera', 'label'])
            ->recent(50)
            ->get();

        $totalToday = DetectionLog::today()->count();

        return Inertia::render('Detections', [
            'initialLogs' => $initialLogs,
            'totalToday' => $totalToday,
        ]);
    }

    public function reports(): Response
    {
        $pendingReports = CitizenReport::pending()
            ->with('verifier')
            ->orderByDesc('created_at')
            ->get();

        $historyReports = CitizenReport::whereIn('status', ['verified', 'rejected'])
            ->with('verifier')
            ->orderByDesc('updated_at')
            ->take(20)
            ->get();

        return Inertia::render('Reports', [
            'pendingReports' => $pendingReports,
            'historyReports' => $historyReports,
        ]);
    }

}
