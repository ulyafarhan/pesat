<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CitizenReportResource\Pages;
use App\Models\CitizenReport;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class CitizenReportResource extends Resource
{
    protected static ?string $model = CitizenReport::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-document-text';
    protected static \UnitEnum|string|null $navigationGroup = 'Layanan Operasional';
    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Laporan Warga';

    protected static ?string $pluralModelLabel = 'Laporan Warga';

    protected static ?string $modelLabel = 'Laporan';

    public static function form(Schema $form): Schema
    {
        return $form
            ->components([
                Forms\Components\TextInput::make('location_name')
                    ->label('Nama Lokasi')
                    ->disabled(),
                Forms\Components\TextInput::make('latitude')
                    ->label('Latitude')
                    ->disabled(),
                Forms\Components\TextInput::make('longitude')
                    ->label('Longitude')
                    ->disabled(),
                Forms\Components\DateTimePicker::make('reported_at')
                    ->label('Dilaporkan Pada')
                    ->disabled(),
                Forms\Components\TextInput::make('media_path')
                    ->label('Path Media Bukti')
                    ->disabled(),
                Forms\Components\Select::make('status')
                    ->label('Status Verifikasi')
                    ->options([
                        'pending' => 'Menunggu Verifikasi',
                        'verified' => 'Terverifikasi',
                        'rejected' => 'Ditolak',
                    ])
                    ->required(),
                Forms\Components\Textarea::make('verification_notes')
                    ->label('Catatan Tindakan Lapangan')
                    ->maxLength(65535),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->poll(config('broadcasting.default') === 'reverb' ? null : '5s')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID Laporan')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('location_name')
                    ->label('Nama Lokasi')
                    ->searchable(),
                Tables\Columns\TextColumn::make('reported_at')
                    ->label('Waktu Laporan')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Menunggu Verifikasi',
                        'verified' => 'Terverifikasi',
                        'rejected' => 'Ditolak',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'verified' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->filters([
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCitizenReports::route('/'),
            'edit' => Pages\EditCitizenReport::route('/{record}/edit'),
        ];
    }
}
