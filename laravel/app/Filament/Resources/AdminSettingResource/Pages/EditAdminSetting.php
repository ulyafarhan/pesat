<?php

namespace App\Filament\Resources\AdminSettingResource\Pages;

use App\Filament\Resources\AdminSettingResource;
use Filament\Resources\Pages\EditRecord;

class EditAdminSetting extends EditRecord
{
    protected static string $resource = AdminSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}


