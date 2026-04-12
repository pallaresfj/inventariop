<?php

namespace App\Filament\Resources\Comunidads;

use App\Filament\Resources\Comunidads\Pages\CreateComunidad;
use App\Filament\Resources\Comunidads\Pages\EditComunidad;
use App\Filament\Resources\Comunidads\Pages\ListComunidads;
use App\Filament\Resources\Comunidads\Schemas\ComunidadForm;
use App\Filament\Resources\Comunidads\Tables\ComunidadsTable;
use App\Models\Comunidad;
use App\Models\User;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ComunidadResource extends Resource
{
    protected static ?string $model = Comunidad::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Inventario';

    protected static ?string $modelLabel = 'Comunidad';

    protected static ?string $pluralModelLabel = 'Comunidades';

    public static function form(Schema $schema): Schema
    {
        return ComunidadForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ComunidadsTable::configure($table);
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
            'index' => ListComunidads::route('/'),
            'create' => CreateComunidad::route('/create'),
            'edit' => EditComunidad::route('/{record}/edit'),
        ];
    }
}
