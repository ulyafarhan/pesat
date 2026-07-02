<?php

namespace App\Filament\Resources\CameraResource\Pages;

use App\Filament\Resources\CameraResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCameras extends ListRecords
{
    protected static string $resource = CameraResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getListeners(): array
    {
        if (config('broadcasting.default') !== 'reverb') {
            return [];
        }

        return [
            'echo:pesat-telemetry,.telemetry.updated' => '$refresh',
        ];
    }
}


