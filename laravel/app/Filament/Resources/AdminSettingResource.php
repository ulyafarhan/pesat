<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdminSettingResource\Pages;
use App\Models\AdminSetting;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class AdminSettingResource extends Resource
{
    protected static ?string $model = AdminSetting::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static \UnitEnum|string|null $navigationGroup = 'Sistem & Keamanan';
    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Pengaturan Admin';

    protected static ?string $pluralModelLabel = 'Pengaturan Admin';

    protected static ?string $modelLabel = 'Pengaturan';

    public static function form(Schema $form): Schema
    {
        return $form
            ->components([
                Forms\Components\TextInput::make('key')
                    ->disabled()
                    ->required(),
                Forms\Components\TextInput::make('value')
                    ->required()
                    ->maxLength(65535),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->poll('5s')
            ->columns([
                Tables\Columns\TextColumn::make('key')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('value')
                    ->searchable(),
            ])
            ->filters([
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
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
            'index' => Pages\ListAdminSettings::route('/'),
            'edit' => Pages\EditAdminSetting::route('/{record}/edit'),
        ];
    }
}
