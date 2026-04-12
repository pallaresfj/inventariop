<?php

namespace App\Filament\Resources\ParishRoles\Pages;

use App\Filament\Resources\ParishRoles\ParishRoleResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListParishRoles extends ListRecords
{
    protected static string $resource = ParishRoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
