<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CameraResource\Pages;
use App\Models\Camera;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class CameraResource extends Resource
{
    protected static ?string $model = Camera::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-video-camera';
    protected static \UnitEnum|string|null $navigationGroup = 'Layanan Operasional';
    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Kamera CCTV';

    protected static ?string $pluralModelLabel = 'Kamera CCTV';

    protected static ?string $modelLabel = 'Kamera';

    public static function form(Schema $form): Schema
    {
        return $form
            ->components([
                Forms\Components\TextInput::make('id')
                    ->required()
                    ->maxLength(50)
                    ->disabled(fn (string $context): bool => $context === 'edit'),
                Forms\Components\TextInput::make('location_name')
                    ->label('Nama Lokasi')
                    ->required()
                    ->maxLength(150),
                Forms\Components\TextInput::make('latitude')
                    ->label('Latitude')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('longitude')
                    ->label('Longitude')
                    ->required()
                    ->numeric(),
                Forms\Components\Toggle::make('is_active')
                    ->label('Status Aktif')
                    ->default(true),
                Forms\Components\TextInput::make('stream_source')
                    ->label('Sumber Aliran (Stream Source)')
                    ->required()
                    ->maxLength(255)
                    ->default('0')
                    ->helperText('Sumber aliran: indeks webcam (misal: 0), URL RTSP (misal: rtsp://...), atau path absolut ke folder snapshot FTP lokal.'),
                Forms\Components\TextInput::make('edge_device_id')
                    ->label('ID Perangkat Edge')
                    ->required()
                    ->maxLength(100)
                    ->default('LAPTOP-JURI')
                    ->helperText('Nama hostname PC/Mini PC yang akan memproses kamera ini. (Contoh: DESKTOP-CKUH365)'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->poll(config('broadcasting.default') === 'reverb' ? null : '5s')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID Kamera')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('location_name')
                    ->label('Nama Lokasi')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('latitude')
                    ->label('Latitude'),
                Tables\Columns\TextColumn::make('longitude')
                    ->label('Longitude'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status Aktif')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('stream_source')
                    ->label('Sumber Aliran')
                    ->searchable(),
            ])
            ->filters([
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    BulkAction::make('change_edge_device')
                        ->label('Ubah ID Perangkat Edge')
                        ->icon('heroicon-o-pencil-square')
                        ->form([
                            Forms\Components\TextInput::make('new_edge_device_id')
                                ->label('ID Perangkat Edge Baru')
                                ->required()
                                ->maxLength(100),
                        ])
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records, array $data): void {
                            foreach ($records as $record) {
                                $record->update(['edge_device_id' => $data['new_edge_device_id']]);
                            }
                        })
                        ->deselectRecordsAfterCompletion(),
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
            'index' => Pages\ListCameras::route('/'),
            'create' => Pages\CreateCamera::route('/create'),
            'edit' => Pages\EditCamera::route('/{record}/edit'),
        ];
    }
}
