<?php

namespace App\Filament\Resources\CargoParroquials;

use App\Filament\Resources\CargoParroquials\Pages\CreateCargoParroquial;
use App\Filament\Resources\CargoParroquials\Pages\EditCargoParroquial;
use App\Filament\Resources\CargoParroquials\Pages\ListCargoParroquials;
use App\Filament\Resources\CargoParroquials\Schemas\CargoParroquialForm;
use App\Filament\Resources\CargoParroquials\Tables\CargoParroquialsTable;
use App\Models\CargoParroquial;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CargoParroquialResource extends Resource
{
    protected static ?string $model = CargoParroquial::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Configuración';

    protected static ?string $modelLabel = 'Cargo Parroquial';

    protected static ?string $pluralModelLabel = 'Cargos Parroquiales';

    public static function form(Schema $schema): Schema
    {
        return CargoParroquialForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CargoParroquialsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCargoParroquials::route('/'),
            'create' => CreateCargoParroquial::route('/create'),
            'edit' => EditCargoParroquial::route('/{record}/edit'),
        ];
    }
}
