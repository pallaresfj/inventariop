<?php

namespace App\Filament\Resources\Communities\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CommunityForm
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
                TextInput::make('name')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(80),
                TextInput::make('legacy_login')
                    ->label('Acceso legado')
                    ->maxLength(255),
                TextInput::make('email')
                    ->label('Correo')
                    ->email()
                    ->maxLength(120),
                TextInput::make('phone')
                    ->label('Telefono')
                    ->maxLength(30),
                TextInput::make('address')
                    ->label('Direccion')
                    ->maxLength(120)
                    ->columnSpanFull(),
                Textarea::make('description')
                    ->label('Descripcion')
                    ->rows(3)
                    ->columnSpanFull(),
                FileUpload::make('image_path')
                    ->label('Imagen')
                    ->disk('public')
                    ->directory('inventory/communities')
                    ->image()
                    ->columnSpanFull(),
            ]);
    }
}
