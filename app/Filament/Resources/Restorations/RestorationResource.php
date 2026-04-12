<?php

namespace App\Filament\Resources\Restorations;

use App\Filament\Resources\Restorations\Pages\CreateRestoration;
use App\Filament\Resources\Restorations\Pages\EditRestoration;
use App\Filament\Resources\Restorations\Pages\ListRestorations;
use App\Filament\Resources\Restorations\Schemas\RestorationForm;
use App\Filament\Resources\Restorations\Tables\RestorationsTable;
use App\Models\Restoration;
use App\Models\User;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class RestorationResource extends Resource
{
    protected static ?string $model = Restoration::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Inventory';

    protected static ?string $modelLabel = 'Restoration';

    protected static ?string $pluralModelLabel = 'Restorations';

    public static function form(Schema $schema): Schema
    {
        return RestorationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RestorationsTable::configure($table);
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
            'index' => ListRestorations::route('/'),
            'create' => CreateRestoration::route('/create'),
            'edit' => EditRestoration::route('/{record}/edit'),
        ];
    }
}
