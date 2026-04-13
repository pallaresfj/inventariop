<?php

namespace App\Filament\Resources\ParishRoles\Pages;

use App\Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\ParishRoles\ParishRoleResource;
use Filament\Actions\DeleteAction;

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
