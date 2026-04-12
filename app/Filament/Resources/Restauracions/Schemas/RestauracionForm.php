<?php

namespace App\Filament\Resources\Restauracions\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class RestauracionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('articulo_id')
                    ->relationship('articulo', 'nombre')
                    ->required()
                    ->searchable()
                    ->preload(),
                DatePicker::make('fecha_restauracion')
                    ->required(),
                TextInput::make('costo_restauracion')
                    ->numeric()
                    ->minValue(0),
                FileUpload::make('imagen_path')
                    ->label('Imagen')
                    ->disk('public')
                    ->directory('inventario/restauraciones')
                    ->image()
                    ->columnSpanFull(),
            ]);
    }
}
