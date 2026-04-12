<?php

namespace App\Filament\Resources\Priests\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PriestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(120),
                Select::make('priest_title_id')
                    ->label('Titulo sacerdotal')
                    ->relationship('priestTitle', 'title')
                    ->searchable()
                    ->preload(),
                TextInput::make('phone')
                    ->label('Telefono')
                    ->maxLength(30),
                TextInput::make('email')
                    ->label('Correo')
                    ->email()
                    ->maxLength(120),
                Textarea::make('bio')
                    ->label('Curriculo')
                    ->rows(4)
                    ->columnSpanFull(),
                FileUpload::make('image_path')
                    ->label('Imagen')
                    ->disk('public')
                    ->directory('inventory/priests')
                    ->image()
                    ->columnSpanFull(),
            ]);
    }
}
