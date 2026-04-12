<?php

namespace App\Filament\Resources\Restorations\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class RestorationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('item_id')
                    ->relationship('item', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                DatePicker::make('restored_at')
                    ->required(),
                TextInput::make('restoration_cost')
                    ->numeric()
                    ->minValue(0),
                FileUpload::make('image_path')
                    ->label('Image')
                    ->disk('public')
                    ->directory('inventory/restorations')
                    ->image()
                    ->columnSpanFull(),
            ]);
    }
}
