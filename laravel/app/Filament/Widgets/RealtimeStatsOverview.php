<?php

namespace App\Filament\Widgets;

use App\Models\Camera;
use App\Models\CitizenReport;
use App\Models\DetectionLog;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Isolate;
use Livewire\Attributes\Lazy;

#[Lazy]
#[Isolate]
class RealtimeStatsOverview extends BaseWidget
{
    protected static ?int $sort = 2;

    public function getListeners(): array
    {
        if (config('broadcasting.default') !== 'reverb') {
            return [];
        }

        return [
            'echo:pesat-telemetry,.telemetry.updated' => '$refresh',
            'echo:pesat-reports,.report.submitted' => '$refresh',
        ];
    }

    protected function getPollingInterval(): ?string
    {
        return config('broadcasting.default') === 'reverb' ? null : '5s';
    }

    protected function getStats(): array
    {
        $todayStart = Carbon::today();

        $totalDetections = DetectionLog::where('created_at', '>=', $todayStart)->count();
        $activeCameras = Camera::where('is_active', true)->count();
        $pendingReports = CitizenReport::where('status', 'pending')->count();

        return [
            Stat::make('Deteksi AI Hari Ini', number_format($totalDetections))
                ->description('Total pelanggaran terdeteksi')
                ->descriptionIcon('heroicon-m-eye')
                ->color('primary'),

            Stat::make('Kamera CCTV Aktif', number_format($activeCameras))
                ->description('Memantau secara real-time')
                ->descriptionIcon('heroicon-m-video-camera')
                ->color('success'),

            Stat::make('Laporan Warga (Pending)', number_format($pendingReports))
                ->description('Menunggu verifikasi petugas')
                ->descriptionIcon('heroicon-m-document-text')
                ->color($pendingReports > 0 ? 'warning' : 'gray'),
        ];
    }
}
