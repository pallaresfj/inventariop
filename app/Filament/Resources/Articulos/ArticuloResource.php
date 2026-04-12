<?php

namespace App\Filament\Resources\Articulos;

use App\Filament\Resources\Articulos\Pages\CreateArticulo;
use App\Filament\Resources\Articulos\Pages\EditArticulo;
use App\Filament\Resources\Articulos\Pages\ListArticulos;
use App\Filament\Resources\Articulos\Schemas\ArticuloForm;
use App\Filament\Resources\Articulos\Tables\ArticulosTable;
use App\Models\Articulo;
use App\Models\User;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ArticuloResource extends Resource
{
    protected static ?string $model = Articulo::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Inventario';

    protected static ?string $modelLabel = 'Artículo';

    protected static ?string $pluralModelLabel = 'Artículos';

    public static function form(Schema $schema): Schema
    {
        return ArticuloForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ArticulosTable::configure($table);
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
            'index' => ListArticulos::route('/'),
            'create' => CreateArticulo::route('/create'),
            'edit' => EditArticulo::route('/{record}/edit'),
        ];
    }
}
