<?php

namespace App\Filament\Resources\Parroquias;

use App\Filament\Resources\Parroquias\Pages\CreateParroquia;
use App\Filament\Resources\Parroquias\Pages\EditParroquia;
use App\Filament\Resources\Parroquias\Pages\ListParroquias;
use App\Filament\Resources\Parroquias\Schemas\ParroquiaForm;
use App\Filament\Resources\Parroquias\Tables\ParroquiasTable;
use App\Models\Parroquia;
use App\Models\User;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ParroquiaResource extends Resource
{
    protected static ?string $model = Parroquia::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Inventario';

    protected static ?string $modelLabel = 'Parroquia';

    protected static ?string $pluralModelLabel = 'Parroquias';

    public static function form(Schema $schema): Schema
    {
        return ParroquiaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ParroquiasTable::configure($table);
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
            'index' => ListParroquias::route('/'),
            'create' => CreateParroquia::route('/create'),
            'edit' => EditParroquia::route('/{record}/edit'),
        ];
    }
}
