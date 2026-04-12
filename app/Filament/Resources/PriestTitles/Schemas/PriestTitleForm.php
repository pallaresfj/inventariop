<?php

namespace App\Filament\Resources\PriestTitles\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PriestTitleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Titulo')
                    ->required()
                    ->maxLength(60),
                TextInput::make('description')
                    ->label('Descripcion')
                    ->maxLength(255),
            ]);
    }
}
