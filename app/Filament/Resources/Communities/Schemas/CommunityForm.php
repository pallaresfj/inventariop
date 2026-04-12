<?php

namespace App\Filament\Resources\Communities\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CommunityForm
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
                TextInput::make('name')
                    ->required()
                    ->maxLength(80),
                TextInput::make('legacy_login')
                    ->label('Legacy login')
                    ->maxLength(255),
                TextInput::make('email')
                    ->email()
                    ->maxLength(120),
                TextInput::make('phone')
                    ->maxLength(30),
                TextInput::make('address')
                    ->maxLength(120)
                    ->columnSpanFull(),
                Textarea::make('description')
                    ->rows(3)
                    ->columnSpanFull(),
                FileUpload::make('image_path')
                    ->label('Image')
                    ->disk('public')
                    ->directory('inventory/communities')
                    ->image()
                    ->columnSpanFull(),
            ]);
    }
}
