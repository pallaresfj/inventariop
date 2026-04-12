<?php

namespace App\Filament\Resources\Parishes\Pages;

use App\Filament\Resources\Parishes\ParishResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditParish extends EditRecord
{
    protected static string $resource = ParishResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
