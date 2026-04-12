<?php

namespace App\Filament\Resources\PriestTitles;

use App\Filament\Resources\PriestTitles\Pages\CreatePriestTitle;
use App\Filament\Resources\PriestTitles\Pages\EditPriestTitle;
use App\Filament\Resources\PriestTitles\Pages\ListPriestTitles;
use App\Filament\Resources\PriestTitles\Schemas\PriestTitleForm;
use App\Filament\Resources\PriestTitles\Tables\PriestTitlesTable;
use App\Models\PriestTitle;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PriestTitleResource extends Resource
{
    protected static ?string $model = PriestTitle::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Configuration';

    protected static ?string $modelLabel = 'Priest Title';

    protected static ?string $pluralModelLabel = 'Priest Titles';

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
