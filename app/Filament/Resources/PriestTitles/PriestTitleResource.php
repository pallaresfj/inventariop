<?php

namespace App\Filament\Resources\PriestTitles;

use App\Filament\Resources\PriestTitles\Pages\CreatePriestTitle;
use App\Filament\Resources\PriestTitles\Pages\EditPriestTitle;
use App\Filament\Resources\PriestTitles\Pages\ListPriestTitles;
use App\Filament\Resources\PriestTitles\Schemas\PriestTitleForm;
use App\Filament\Resources\PriestTitles\Tables\PriestTitlesTable;
use App\Models\PriestTitle;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class PriestTitleResource extends Resource
{
    protected static ?string $model = PriestTitle::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Configuracion';

    protected static ?string $navigationLabel = 'Titulos sacerdotales';

    protected static ?string $modelLabel = 'Titulo sacerdotal';

    protected static ?string $pluralModelLabel = 'Titulos sacerdotales';

    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return PriestTitleForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PriestTitlesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPriestTitles::route('/'),
            'create' => CreatePriestTitle::route('/create'),
            'edit' => EditPriestTitle::route('/{record}/edit'),
        ];
    }
}
