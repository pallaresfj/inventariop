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
                    ->label('Parroquia')
                    ->relationship('parish', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                Select::make('priest_id')
                    ->label('Sacerdote')
                    ->relationship('priest', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                Select::make('parish_role_id')
                    ->label('Cargo parroquial')
                    ->relationship('parishRole', 'description')
                    ->searchable()
                    ->preload(),
                Toggle::make('is_current')
                    ->label('Vigente')
                    ->default(true),
            ]);
    }
}
