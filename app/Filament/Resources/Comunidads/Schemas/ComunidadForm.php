<?php

namespace App\Filament\Resources\Comunidads\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ComunidadForm
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
                TextInput::make('nombre')
                    ->required()
                    ->maxLength(80),
                TextInput::make('legacy_login')
                    ->label('Login legado')
                    ->maxLength(255),
                TextInput::make('correo')
                    ->email()
                    ->maxLength(120),
                TextInput::make('telefono')
                    ->maxLength(30),
                TextInput::make('direccion')
                    ->maxLength(120)
                    ->columnSpanFull(),
                Textarea::make('descripcion')
                    ->rows(3)
                    ->columnSpanFull(),
                FileUpload::make('imagen_path')
                    ->label('Imagen')
                    ->disk('public')
                    ->directory('inventario/comunidades')
                    ->image()
                    ->columnSpanFull(),
            ]);
    }
}
