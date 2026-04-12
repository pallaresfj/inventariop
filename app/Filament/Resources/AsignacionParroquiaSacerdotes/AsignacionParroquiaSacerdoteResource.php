<?php

namespace App\Filament\Resources\AsignacionParroquiaSacerdotes;

use App\Filament\Resources\AsignacionParroquiaSacerdotes\Pages\CreateAsignacionParroquiaSacerdote;
use App\Filament\Resources\AsignacionParroquiaSacerdotes\Pages\EditAsignacionParroquiaSacerdote;
use App\Filament\Resources\AsignacionParroquiaSacerdotes\Pages\ListAsignacionParroquiaSacerdotes;
use App\Filament\Resources\AsignacionParroquiaSacerdotes\Schemas\AsignacionParroquiaSacerdoteForm;
use App\Filament\Resources\AsignacionParroquiaSacerdotes\Tables\AsignacionParroquiaSacerdotesTable;
use App\Models\AsignacionParroquiaSacerdote;
use App\Models\User;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class AsignacionParroquiaSacerdoteResource extends Resource
{
    protected static ?string $model = AsignacionParroquiaSacerdote::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Inventario';

    protected static ?string $modelLabel = 'Asignación Sacerdotal';

    protected static ?string $pluralModelLabel = 'Asignaciones Sacerdotales';

    public static function form(Schema $schema): Schema
    {
        return AsignacionParroquiaSacerdoteForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AsignacionParroquiaSacerdotesTable::configure($table);
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
            'index' => ListAsignacionParroquiaSacerdotes::route('/'),
            'create' => CreateAsignacionParroquiaSacerdote::route('/create'),
            'edit' => EditAsignacionParroquiaSacerdote::route('/{record}/edit'),
        ];
    }
}
