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
                Select::make('arciprestazgo_id')
                    ->relationship('arciprestazgo', 'nombre')
                    ->searchable()
                    ->preload(),
                Select::make('parroquia_id')
                    ->relationship('parroquia', 'nombre')
                    ->searchable()
                    ->preload(),
                Select::make('comunidad_id')
                    ->relationship('comunidad', 'nombre')
                    ->searchable()
                    ->preload(),
                Toggle::make('is_active')
                    ->label('Usuario activo')
                    ->default(true),
                Toggle::make('force_password_reset')
                    ->label('Forzar cambio de contraseña')
                    ->default(false),
                FileUpload::make('picture_path')
                    ->label('Foto')
                    ->disk('public')
                    ->directory('inventario/users')
                    ->image(),
            ]);
    }
}
