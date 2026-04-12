<?php

namespace App\Filament\Resources\Items\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class ItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Articulo')
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('General')
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nombre')
                                    ->required()
                                    ->maxLength(120)
                                    ->columnSpanFull(),
                                Select::make('parish_id')
                                    ->label('Parroquia')
                                    ->relationship('parish', 'name')
                                    ->required()
                                    ->searchable()
                                    ->preload(),
                                Select::make('community_id')
                                    ->label('Comunidad')
                                    ->relationship('community', 'name')
                                    ->required()
                                    ->searchable()
                                    ->preload(),
                                Select::make('condition')
                                    ->label('Estado')
                                    ->options([
                                        'B' => 'Bueno',
                                        'M' => 'Malo',
                                        'R' => 'Restaurado',
                                    ])
                                    ->required(),
                                Toggle::make('is_active')
                                    ->label('Activo')
                                    ->default(true),
                            ])
                            ->columns(2),
                        Tab::make('Detalles')
                            ->schema([
                                TextInput::make('price')
                                    ->label('Precio')
                                    ->numeric()
                                    ->minValue(0)
                                    ->default(0)
                                    ->required(),
                                DatePicker::make('acquired_at')
                                    ->label('Fecha de adquisicion'),
                                Textarea::make('description')
                                    ->label('Descripcion')
                                    ->rows(4)
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),
                        Tab::make('Multimedia')
                            ->schema([
                                FileUpload::make('image_path')
                                    ->label('Imagen')
                                    ->disk('public')
                                    ->directory('inventory/items')
                                    ->image()
                                    ->columnSpanFull(),
                            ]),
                    ]),
            ]);
    }
}
