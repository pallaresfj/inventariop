<?php

namespace App\Filament\Resources\Parroquias\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ParroquiaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('arciprestazgo_id')
                    ->relationship('arciprestazgo', 'nombre')
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
                TextInput::make('web')
                    ->maxLength(120),
                TextInput::make('direccion')
                    ->maxLength(120)
                    ->columnSpanFull(),
                Textarea::make('descripcion')
                    ->rows(3)
                    ->columnSpanFull(),
                FileUpload::make('imagen_path')
                    ->label('Imagen')
                    ->disk('public')
                    ->directory('inventario/parroquias')
                    ->image()
                    ->columnSpanFull(),
            ]);
    }
}
