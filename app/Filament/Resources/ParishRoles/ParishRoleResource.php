<?php

namespace App\Filament\Resources\ParishRoles;

use App\Filament\Resources\ParishRoles\Pages\CreateParishRole;
use App\Filament\Resources\ParishRoles\Pages\EditParishRole;
use App\Filament\Resources\ParishRoles\Pages\ListParishRoles;
use App\Filament\Resources\ParishRoles\Schemas\ParishRoleForm;
use App\Filament\Resources\ParishRoles\Tables\ParishRolesTable;
use App\Models\ParishRole;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ParishRoleResource extends Resource
{
    protected static ?string $model = ParishRole::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBriefcase;

    protected static string|UnitEnum|null $navigationGroup = 'Configuracion';

    protected static ?string $navigationLabel = 'Cargos parroquiales';

    protected static ?string $modelLabel = 'Cargo parroquial';

    protected static ?string $pluralModelLabel = 'Cargos parroquiales';

    protected static ?int $navigationSort = 20;

    public static function form(Schema $schema): Schema
    {
        return ParishRoleForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ParishRolesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListParishRoles::route('/'),
            'create' => CreateParishRole::route('/create'),
            'edit' => EditParishRole::route('/{record}/edit'),
        ];
    }
}
