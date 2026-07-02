<?php

namespace App\Filament\Resources\AdminSettingResource\Pages;

use App\Filament\Resources\AdminSettingResource;
use Filament\Resources\Pages\ListRecords;

class ListAdminSettings extends ListRecords
{
    protected static string $resource = AdminSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}


