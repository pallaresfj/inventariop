<?php

namespace App\Filament\Resources\Priests\Pages;

use App\Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\Priests\PriestResource;
use Filament\Actions\DeleteAction;

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
