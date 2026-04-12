<?php

namespace App\Filament\Resources\Restauracions;

use App\Filament\Resources\Restauracions\Pages\CreateRestauracion;
use App\Filament\Resources\Restauracions\Pages\EditRestauracion;
use App\Filament\Resources\Restauracions\Pages\ListRestauracions;
use App\Filament\Resources\Restauracions\Schemas\RestauracionForm;
use App\Filament\Resources\Restauracions\Tables\RestauracionsTable;
use App\Models\Restauracion;
use App\Models\User;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class RestauracionResource extends Resource
{
    protected static ?string $model = Restauracion::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Inventario';

    protected static ?string $modelLabel = 'Restauración';

    protected static ?string $pluralModelLabel = 'Restauraciones';

    public static function form(Schema $schema): Schema
    {
        return RestauracionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RestauracionsTable::configure($table);
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
            'index' => ListRestauracions::route('/'),
            'create' => CreateRestauracion::route('/create'),
            'edit' => EditRestauracion::route('/{record}/edit'),
        ];
    }
}
