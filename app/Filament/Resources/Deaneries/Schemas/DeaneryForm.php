<?php

namespace App\Filament\Resources\Deaneries\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class DeaneryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Arciprestazgo')
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('General')
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nombre')
                                    ->required()
                                    ->maxLength(80),
                                Select::make('archpriest_id')
                                    ->label('Arcipreste')
                                    ->relationship('archpriest', 'name')
                                    ->searchable()
                                    ->preload(),
                            ])
                            ->columns(2),
                        Tab::make('Contacto')
                            ->schema([
                                TextInput::make('email')
                                    ->label('Correo')
                                    ->email()
                                    ->maxLength(120),
                            ]),
                        Tab::make('Contenido')
                            ->schema([
                                RichEditor::make('description')
                                    ->label('Descripcion')
                                    ->columnSpanFull(),
                            ])
                            ->columns(1),
                        Tab::make('Multimedia')
                            ->schema([
                                FileUpload::make('image_path')
                                    ->label('Imagen')
                                    ->disk('public')
                                    ->directory('inventory/deaneries')
                                    ->image()
                                    ->columnSpanFull(),
                            ]),
                    ]),
            ]);
    }
}
