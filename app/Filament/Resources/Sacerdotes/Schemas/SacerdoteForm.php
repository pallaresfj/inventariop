<?php

namespace App\Filament\Resources\Sacerdotes\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SacerdoteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nombre')
                    ->required()
                    ->maxLength(120),
                Select::make('titulo_sacerdotal_id')
                    ->relationship('tituloSacerdotal', 'titulo')
                    ->searchable()
                    ->preload(),
                TextInput::make('telefono')
                    ->maxLength(30),
                TextInput::make('correo')
                    ->email()
                    ->maxLength(120),
                Textarea::make('curriculo')
                    ->rows(4)
                    ->columnSpanFull(),
                FileUpload::make('imagen_path')
                    ->label('Imagen')
                    ->disk('public')
                    ->directory('inventario/sacerdotes')
                    ->image()
                    ->columnSpanFull(),
            ]);
    }
}
