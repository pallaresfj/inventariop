<?php

namespace App\Filament\Resources\PriestTitles\Pages;

use App\Filament\Resources\PriestTitles\PriestTitleResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPriestTitle extends EditRecord
{
    protected static string $resource = PriestTitleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
