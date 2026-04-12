<?php

namespace App\Filament\Resources\Priests;

use App\Filament\Resources\Priests\Pages\CreatePriest;
use App\Filament\Resources\Priests\Pages\EditPriest;
use App\Filament\Resources\Priests\Pages\ListPriests;
use App\Filament\Resources\Priests\Schemas\PriestForm;
use App\Filament\Resources\Priests\Tables\PriestsTable;
use App\Models\Priest;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class PriestResource extends Resource
{
    protected static ?string $model = Priest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Inventario';

    protected static ?string $navigationLabel = 'Sacerdotes';

    protected static ?string $modelLabel = 'Sacerdote';

    protected static ?string $pluralModelLabel = 'Sacerdotes';

    protected static ?int $navigationSort = 60;

    public static function form(Schema $schema): Schema
    {
        return PriestForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PriestsTable::configure($table);
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
            'index' => ListPriests::route('/'),
            'create' => CreatePriest::route('/create'),
            'edit' => EditPriest::route('/{record}/edit'),
        ];
    }
}
