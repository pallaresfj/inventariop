<?php

namespace App\Filament\Resources\Priests\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PriestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(120),
                Select::make('priest_title_id')
                    ->relationship('priestTitle', 'title')
                    ->searchable()
                    ->preload(),
                TextInput::make('phone')
                    ->maxLength(30),
                TextInput::make('email')
                    ->email()
                    ->maxLength(120),
                Textarea::make('bio')
                    ->rows(4)
                    ->columnSpanFull(),
                FileUpload::make('image_path')
                    ->label('Image')
                    ->disk('public')
                    ->directory('inventory/priests')
                    ->image()
                    ->columnSpanFull(),
            ]);
    }
}
