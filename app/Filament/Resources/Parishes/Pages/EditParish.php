<?php

namespace App\Filament\Resources\Parishes\Pages;

use App\Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\Parishes\ParishResource;
use Filament\Actions\DeleteAction;

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
