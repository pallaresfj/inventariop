<?php

namespace App\Filament\Resources\TituloSacerdotals\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TituloSacerdotalForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('titulo')
                    ->required()
                    ->maxLength(60),
                TextInput::make('descripcion')
                    ->maxLength(255),
            ]);
    }
}
