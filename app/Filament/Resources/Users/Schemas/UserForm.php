<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('username')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->email()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                TextInput::make('password')
                    ->password()
                    ->revealable()
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->maxLength(255),
                Select::make('roles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable(),
                Select::make('deanery_id')
                    ->relationship('deanery', 'name')
                    ->searchable()
                    ->preload(),
                Select::make('parish_id')
                    ->relationship('parish', 'name')
                    ->searchable()
                    ->preload(),
                Select::make('community_id')
                    ->relationship('community', 'name')
                    ->searchable()
                    ->preload(),
                Toggle::make('is_active')
                    ->label('Active user')
                    ->default(true),
                Toggle::make('force_password_reset')
                    ->label('Force password reset')
                    ->default(false),
                FileUpload::make('picture_path')
                    ->label('Photo')
                    ->disk('public')
                    ->directory('inventory/users')
                    ->image(),
            ]);
    }
}
