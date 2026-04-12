<?php

namespace App\Filament\Resources\Items\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('parish_id')
                    ->relationship('parish', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                Select::make('community_id')
                    ->relationship('community', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                TextInput::make('name')
                    ->required()
                    ->maxLength(120),
                Select::make('condition')
                    ->options([
                        'B' => 'Good',
                        'M' => 'Poor',
                        'R' => 'Restored',
                    ])
                    ->required(),
                TextInput::make('price')
                    ->numeric()
                    ->minValue(0)
                    ->default(0)
                    ->required(),
                DatePicker::make('acquired_at'),
                Toggle::make('is_active')
                    ->default(true),
                Textarea::make('description')
                    ->rows(3)
                    ->columnSpanFull(),
                FileUpload::make('image_path')
                    ->label('Image')
                    ->disk('public')
                    ->directory('inventory/items')
                    ->image()
                    ->columnSpanFull(),
            ]);
    }
}
