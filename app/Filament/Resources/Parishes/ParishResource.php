<?php

namespace App\Filament\Resources\Parishes;

use App\Filament\Resources\Parishes\Pages\CreateParish;
use App\Filament\Resources\Parishes\Pages\EditParish;
use App\Filament\Resources\Parishes\Pages\ListParishes;
use App\Filament\Resources\Parishes\Schemas\ParishForm;
use App\Filament\Resources\Parishes\Tables\ParishesTable;
use App\Models\Parish;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class ParishResource extends Resource
{
    protected static ?string $model = Parish::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Inventario';

    protected static ?string $navigationLabel = 'Parroquias';

    protected static ?string $modelLabel = 'Parroquia';

    protected static ?string $pluralModelLabel = 'Parroquias';

    protected static ?int $navigationSort = 20;

    public static function form(Schema $schema): Schema
    {
        return ParishForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ParishesTable::configure($table);
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
            'index' => ListParishes::route('/'),
            'create' => CreateParish::route('/create'),
            'edit' => EditParish::route('/{record}/edit'),
        ];
    }
}
