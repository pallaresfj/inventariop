<?php

namespace App\Filament\Resources\Deaneries\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class DeaneryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(80),
                TextInput::make('email')
                    ->email()
                    ->maxLength(120),
                Select::make('archpriest_id')
                    ->relationship('archpriest', 'name')
                    ->searchable()
                    ->preload(),
                Textarea::make('description')
                    ->rows(3)
                    ->columnSpanFull(),
                FileUpload::make('image_path')
                    ->label('Image')
                    ->disk('public')
                    ->directory('inventory/deaneries')
                    ->image()
                    ->columnSpanFull(),
            ]);
    }
}
