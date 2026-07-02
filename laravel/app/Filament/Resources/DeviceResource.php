<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DeviceResource\Pages;
use App\Models\Camera;
use Filament\Actions\EditAction;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DeviceResource extends Resource
{
    protected static ?string $model = Camera::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-server';
    protected static \UnitEnum|string|null $navigationGroup = 'Infrastruktur Edge & AI';
    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Perangkat Edge';

    protected static ?string $pluralModelLabel = 'Perangkat Edge';

    protected static ?string $modelLabel = 'Perangkat';

    public static function form(Schema $form): Schema
    {
        return $form
            ->components([
                \Filament\Forms\Components\TextInput::make('edge_device_id')
                    ->label('ID Perangkat Edge')
                    ->required()
                    ->maxLength(100)
                    ->helperText('Ubah ID Perangkat ini untuk menyesuaikan dengan hostname PC/Mini PC Anda.'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->poll(config('broadcasting.default') === 'reverb' ? null : '5s')
            ->columns([
                Tables\Columns\TextColumn::make('edge_device_id')
                    ->label('Device ID')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('last_heartbeat_at')
                    ->label('Heartbeat Terakhir')
                    ->dateTime()
                    ->sortable()
                    ->formatStateUsing(fn ($state) => $state ? $state->diffForHumans() : 'Tidak Ada'),
                Tables\Columns\TextColumn::make('edge_metrics.cpu')
                    ->label('CPU %')
                    ->formatStateUsing(fn ($state) => $state !== null ? $state . '%' : '-'),
                Tables\Columns\TextColumn::make('edge_metrics.ram')
                    ->label('RAM %')
                    ->formatStateUsing(fn ($state) => $state !== null ? $state . '%' : '-'),
                Tables\Columns\TextColumn::make('is_active')
                    ->label('Status Kamera')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state ? 'Aktif' : 'Nonaktif')
                    ->color(fn ($state) => $state ? 'success' : 'danger'),
                Tables\Columns\TextColumn::make('id')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(function ($record) {
                        if (!$record->last_heartbeat_at) return 'Offline';
                        return $record->last_heartbeat_at->diffInMinutes() < 5 ? 'Online' : 'Offline';
                    })
                    ->color(fn ($record) => $record->last_heartbeat_at && $record->last_heartbeat_at->diffInMinutes() < 5 ? 'success' : 'danger'),
            ])
            ->defaultSort('last_heartbeat_at', 'desc')
            ->filters([])
            ->actions([
                EditAction::make()
                    ->label('Ubah ID')
                    ->icon('heroicon-o-pencil-square'),
            ])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDevices::route('/'),
        ];
    }
}
