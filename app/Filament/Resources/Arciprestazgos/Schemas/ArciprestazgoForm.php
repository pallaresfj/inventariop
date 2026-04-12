<?php

namespace App\Filament\Resources\Arciprestazgos\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ArciprestazgoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nombre')
                    ->required()
                    ->maxLength(80),
                TextInput::make('correo')
                    ->email()
                    ->maxLength(120),
                Select::make('arcipestre_id')
                    ->relationship('arcipestre', 'nombre')
                    ->searchable()
                    ->preload(),
                Textarea::make('descripcion')
                    ->rows(3)
                    ->columnSpanFull(),
                FileUpload::make('imagen_path')
                    ->label('Imagen')
                    ->disk('public')
                    ->directory('inventario/arciprestazgos')
                    ->image()
                    ->columnSpanFull(),
            ]);
    }
}
