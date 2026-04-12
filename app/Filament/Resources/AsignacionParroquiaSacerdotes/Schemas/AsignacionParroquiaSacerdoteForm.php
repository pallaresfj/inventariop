<?php

namespace App\Filament\Resources\AsignacionParroquiaSacerdotes\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class AsignacionParroquiaSacerdoteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('parroquia_id')
                    ->relationship('parroquia', 'nombre')
                    ->required()
                    ->searchable()
                    ->preload(),
                Select::make('sacerdote_id')
                    ->relationship('sacerdote', 'nombre')
                    ->required()
                    ->searchable()
                    ->preload(),
                Select::make('cargo_parroquial_id')
                    ->relationship('cargoParroquial', 'descripcion')
                    ->searchable()
                    ->preload(),
                Toggle::make('vigente')
                    ->default(true),
            ]);
    }
}
