<?php

namespace App\Filament\Resources\Sacerdotes;

use App\Filament\Resources\Sacerdotes\Pages\CreateSacerdote;
use App\Filament\Resources\Sacerdotes\Pages\EditSacerdote;
use App\Filament\Resources\Sacerdotes\Pages\ListSacerdotes;
use App\Filament\Resources\Sacerdotes\Schemas\SacerdoteForm;
use App\Filament\Resources\Sacerdotes\Tables\SacerdotesTable;
use App\Models\Sacerdote;
use App\Models\User;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class SacerdoteResource extends Resource
{
    protected static ?string $model = Sacerdote::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Inventario';

    protected static ?string $modelLabel = 'Sacerdote';

    protected static ?string $pluralModelLabel = 'Sacerdotes';

    public static function form(Schema $schema): Schema
    {
        return SacerdoteForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SacerdotesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = Auth::user();

        if ($user instanceof User) {
            return $query->visibleTo($user);
        }

        return $query->whereRaw('1 = 0');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSacerdotes::route('/'),
            'create' => CreateSacerdote::route('/create'),
            'edit' => EditSacerdote::route('/{record}/edit'),
        ];
    }
}
