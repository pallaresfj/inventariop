<?php

namespace App\Filament\Resources\Communities;

use App\Filament\Resources\Communities\Pages\CreateCommunity;
use App\Filament\Resources\Communities\Pages\EditCommunity;
use App\Filament\Resources\Communities\Pages\ListCommunities;
use App\Filament\Resources\Communities\RelationManagers\ItemsRelationManager;
use App\Filament\Resources\Communities\Schemas\CommunityForm;
use App\Filament\Resources\Communities\Tables\CommunitiesTable;
use App\Models\Community;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class CommunityResource extends Resource
{
    protected static ?string $model = Community::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static string|UnitEnum|null $navigationGroup = 'Inventario';

    protected static ?string $navigationLabel = 'Comunidades';

    protected static ?string $modelLabel = 'Comunidad';

    protected static ?string $pluralModelLabel = 'Comunidades';

    protected static ?int $navigationSort = 30;

    public static function form(Schema $schema): Schema
    {
        return CommunityForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CommunitiesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            ItemsRelationManager::class,
        ];
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
            'index' => ListCommunities::route('/'),
            'create' => CreateCommunity::route('/create'),
            'edit' => EditCommunity::route('/{record}/edit'),
        ];
    }
}
