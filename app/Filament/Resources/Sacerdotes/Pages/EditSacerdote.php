<?php

namespace App\Filament\Resources\Sacerdotes\Pages;

use App\Filament\Resources\Sacerdotes\SacerdoteResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSacerdote extends EditRecord
{
    protected static string $resource = SacerdoteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
