<?php

namespace App\Filament\Resources\ParishPriestAssignments\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ParishPriestAssignmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('parish_id')
                    ->relationship('parish', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                Select::make('priest_id')
                    ->relationship('priest', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                Select::make('parish_role_id')
                    ->relationship('parishRole', 'description')
                    ->searchable()
                    ->preload(),
                Toggle::make('is_current')
                    ->default(true),
            ]);
    }
}
