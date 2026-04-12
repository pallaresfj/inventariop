<?php

namespace App\Filament\Resources\Priests\Pages;

use App\Filament\Resources\Priests\PriestResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPriest extends EditRecord
{
    protected static string $resource = PriestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
