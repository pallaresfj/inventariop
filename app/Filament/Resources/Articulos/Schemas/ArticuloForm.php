<?php

namespace App\Filament\Resources\Articulos\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ArticuloForm
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
                Select::make('comunidad_id')
                    ->relationship('comunidad', 'nombre')
                    ->required()
                    ->searchable()
                    ->preload(),
                TextInput::make('nombre')
                    ->required()
                    ->maxLength(120),
                Select::make('estado')
                    ->options([
                        'B' => 'Bueno',
                        'M' => 'Malo',
                        'R' => 'Restaurado',
                    ])
                    ->required(),
                TextInput::make('precio')
                    ->numeric()
                    ->minValue(0)
                    ->default(0)
                    ->required(),
                DatePicker::make('fecha_adquisicion'),
                Toggle::make('activo')
                    ->default(true),
                Textarea::make('descripcion')
                    ->rows(3)
                    ->columnSpanFull(),
                FileUpload::make('imagen_path')
                    ->label('Imagen')
                    ->disk('public')
                    ->directory('inventario/articulos')
                    ->image()
                    ->columnSpanFull(),
            ]);
    }
}
