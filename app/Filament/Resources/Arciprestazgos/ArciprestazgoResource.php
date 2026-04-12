<?php

namespace App\Filament\Resources\Arciprestazgos;

use App\Filament\Resources\Arciprestazgos\Pages\CreateArciprestazgo;
use App\Filament\Resources\Arciprestazgos\Pages\EditArciprestazgo;
use App\Filament\Resources\Arciprestazgos\Pages\ListArciprestazgos;
use App\Filament\Resources\Arciprestazgos\Schemas\ArciprestazgoForm;
use App\Filament\Resources\Arciprestazgos\Tables\ArciprestazgosTable;
use App\Models\Arciprestazgo;
use App\Models\User;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ArciprestazgoResource extends Resource
{
    protected static ?string $model = Arciprestazgo::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Inventario';

    protected static ?string $modelLabel = 'Arciprestazgo';

    protected static ?string $pluralModelLabel = 'Arciprestazgos';

    public static function form(Schema $schema): Schema
    {
        return ArciprestazgoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ArciprestazgosTable::configure($table);
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
            'index' => ListArciprestazgos::route('/'),
            'create' => CreateArciprestazgo::route('/create'),
            'edit' => EditArciprestazgo::route('/{record}/edit'),
        ];
    }
}
