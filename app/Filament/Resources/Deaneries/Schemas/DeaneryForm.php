<?php

namespace App\Filament\Resources\Deaneries\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class DeaneryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(80),
                TextInput::make('email')
                    ->label('Correo')
                    ->email()
                    ->maxLength(120),
                Select::make('archpriest_id')
                    ->label('Arcipreste')
                    ->relationship('archpriest', 'name')
                    ->searchable()
                    ->preload(),
                Textarea::make('description')
                    ->label('Descripcion')
                    ->rows(3)
                    ->columnSpanFull(),
                FileUpload::make('image_path')
                    ->label('Imagen')
                    ->disk('public')
                    ->directory('inventory/deaneries')
                    ->image()
                    ->columnSpanFull(),
            ]);
    }
}
