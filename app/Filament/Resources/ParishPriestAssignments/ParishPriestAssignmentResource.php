<?php

namespace App\Filament\Resources\ParishPriestAssignments;

use App\Filament\Resources\ParishPriestAssignments\Pages\CreateParishPriestAssignment;
use App\Filament\Resources\ParishPriestAssignments\Pages\EditParishPriestAssignment;
use App\Filament\Resources\ParishPriestAssignments\Pages\ListParishPriestAssignments;
use App\Filament\Resources\ParishPriestAssignments\Schemas\ParishPriestAssignmentForm;
use App\Filament\Resources\ParishPriestAssignments\Tables\ParishPriestAssignmentsTable;
use App\Models\ParishPriestAssignment;
use App\Models\User;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ParishPriestAssignmentResource extends Resource
{
    protected static ?string $model = ParishPriestAssignment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Inventory';

    protected static ?string $modelLabel = 'Parish Priest Assignment';

    protected static ?string $pluralModelLabel = 'Parish Priest Assignments';

    public static function form(Schema $schema): Schema
    {
        return ParishPriestAssignmentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ParishPriestAssignmentsTable::configure($table);
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
            'index' => ListParishPriestAssignments::route('/'),
            'create' => CreateParishPriestAssignment::route('/create'),
            'edit' => EditParishPriestAssignment::route('/{record}/edit'),
        ];
    }
}
