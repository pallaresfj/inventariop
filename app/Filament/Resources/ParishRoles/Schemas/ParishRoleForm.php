<?php

namespace App\Filament\Resources\ParishRoles\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ParishRoleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('description')
                    ->required()
                    ->maxLength(80),
            ]);
    }
}
