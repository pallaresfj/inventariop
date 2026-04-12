<?php

namespace App\Filament\Resources\ParishRoles\Pages;

use App\Filament\Resources\ParishRoles\ParishRoleResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditParishRole extends EditRecord
{
    protected static string $resource = ParishRoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
