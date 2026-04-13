<?php

namespace App\Filament\Resources\Priests\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class PriestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Sacerdote')
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('General')
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nombre')
                                    ->required()
                                    ->maxLength(120),
                                Select::make('priest_title_id')
                                    ->label('Titulo sacerdotal')
                                    ->relationship('priestTitle', 'title')
                                    ->searchable()
                                    ->preload(),
                            ])
                            ->columns(2),
                        Tab::make('Contacto')
                            ->schema([
                                TextInput::make('phone')
                                    ->label('Telefono')
                                    ->maxLength(30),
                                TextInput::make('email')
                                    ->label('Correo')
                                    ->email()
                                    ->maxLength(120),
                            ])
                            ->columns(2),
                        Tab::make('Contenido')
                            ->schema([
                                RichEditor::make('bio')
                                    ->label('Curriculo')
                                    ->columnSpanFull(),
                            ])
                            ->columns(1),
                        Tab::make('Multimedia')
                            ->schema([
                                FileUpload::make('image_path')
                                    ->label('Imagen')
                                    ->disk('public')
                                    ->directory('inventory/priests')
                                    ->image()
                                    ->columnSpanFull(),
                            ]),
                    ]),
            ]);
    }
}
