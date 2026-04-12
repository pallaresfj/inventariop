<?php

namespace App\Filament\Resources\Deaneries;

use App\Filament\Resources\Deaneries\Pages\CreateDeanery;
use App\Filament\Resources\Deaneries\Pages\EditDeanery;
use App\Filament\Resources\Deaneries\Pages\ListDeaneries;
use App\Filament\Resources\Deaneries\Schemas\DeaneryForm;
use App\Filament\Resources\Deaneries\Tables\DeaneriesTable;
use App\Models\Deanery;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class DeaneryResource extends Resource
{
    protected static ?string $model = Deanery::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Inventario';

    protected static ?string $navigationLabel = 'Arciprestazgos';

    protected static ?string $modelLabel = 'Arciprestazgo';

    protected static ?string $pluralModelLabel = 'Arciprestazgos';

    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return DeaneryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DeaneriesTable::configure($table);
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
            'index' => ListDeaneries::route('/'),
            'create' => CreateDeanery::route('/create'),
            'edit' => EditDeanery::route('/{record}/edit'),
        ];
    }
}
