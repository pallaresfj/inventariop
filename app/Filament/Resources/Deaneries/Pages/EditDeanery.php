<?php

namespace App\Filament\Resources\Deaneries\Pages;

use App\Filament\Resources\Deaneries\DeaneryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDeanery extends EditRecord
{
    protected static string $resource = DeaneryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
