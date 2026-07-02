<?php

namespace App\Filament\Resources\CitizenReportResource\Pages;

use App\Filament\Resources\CitizenReportResource;
use Filament\Resources\Pages\ListRecords;

class ListCitizenReports extends ListRecords
{
    protected static string $resource = CitizenReportResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function getListeners(): array
    {
        if (config('broadcasting.default') !== 'reverb') {
            return [];
        }

        return [
            'echo:pesat-reports,.report.submitted' => '$refresh',
            'echo:pesat-telemetry,.telemetry.updated' => '$refresh',
        ];
    }
}


