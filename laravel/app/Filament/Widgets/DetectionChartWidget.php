<?php

namespace App\Filament\Widgets;

use App\Models\DetectionLog;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Isolate;
use Livewire\Attributes\Lazy;

#[Lazy]
#[Isolate]
class DetectionChartWidget extends ChartWidget
{
    protected ?string $heading = 'Tren Deteksi 7 Hari Terakhir';
    
    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    public function getListeners(): array
    {
        if (config('broadcasting.default') !== 'reverb') {
            return [];
        }

        return [
            'echo:pesat-telemetry,.telemetry.updated' => '$refresh',
        ];
    }

    protected function getPollingInterval(): ?string
    {
        return config('broadcasting.default') === 'reverb' ? null : '5s';
    }

    protected function getData(): array
    {
        $data = [];
        $labels = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $labels[] = $date->format('d M');
            
            $count = DetectionLog::whereDate('created_at', $date)->count();
            $data[] = $count;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total Deteksi',
                    'data' => $data,
                    'borderColor' => '#6366f1', // Indigo 500
                    'backgroundColor' => 'rgba(99, 102, 241, 0.15)',
                    'fill' => true,
                    'tension' => 0.4, // Smooth curves
                    'pointBackgroundColor' => '#6366f1',
                    'pointBorderColor' => '#18181b', // Zinc 900 for dark mode contrast
                    'pointBorderWidth' => 2,
                    'pointRadius' => 4,
                    'pointHoverRadius' => 6,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
