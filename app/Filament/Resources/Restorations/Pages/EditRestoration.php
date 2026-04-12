<?php

namespace App\Filament\Resources\Restorations\Pages;

use App\Filament\Resources\Restorations\RestorationResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRestoration extends EditRecord
{
    protected static string $resource = RestorationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
