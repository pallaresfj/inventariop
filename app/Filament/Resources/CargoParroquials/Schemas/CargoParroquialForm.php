<?php

namespace App\Filament\Resources\CargoParroquials\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CargoParroquialForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('descripcion')
                    ->required()
                    ->maxLength(80),
            ]);
    }
}
