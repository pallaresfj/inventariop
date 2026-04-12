<?php

namespace App\Filament\Resources\TituloSacerdotals;

use App\Filament\Resources\TituloSacerdotals\Pages\CreateTituloSacerdotal;
use App\Filament\Resources\TituloSacerdotals\Pages\EditTituloSacerdotal;
use App\Filament\Resources\TituloSacerdotals\Pages\ListTituloSacerdotals;
use App\Filament\Resources\TituloSacerdotals\Schemas\TituloSacerdotalForm;
use App\Filament\Resources\TituloSacerdotals\Tables\TituloSacerdotalsTable;
use App\Models\TituloSacerdotal;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TituloSacerdotalResource extends Resource
{
    protected static ?string $model = TituloSacerdotal::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Configuración';

    protected static ?string $modelLabel = 'Título Sacerdotal';

    protected static ?string $pluralModelLabel = 'Títulos Sacerdotales';

    public static function form(Schema $schema): Schema
    {
        return TituloSacerdotalForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TituloSacerdotalsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTituloSacerdotals::route('/'),
            'create' => CreateTituloSacerdotal::route('/create'),
            'edit' => EditTituloSacerdotal::route('/{record}/edit'),
        ];
    }
}
