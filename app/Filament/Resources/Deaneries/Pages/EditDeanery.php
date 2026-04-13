<?php

namespace App\Filament\Resources\Deaneries\Pages;

use App\Filament\Resources\Deaneries\DeaneryResource;
use App\Filament\Resources\Pages\EditRecord;
use Filament\Actions\DeleteAction;

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
