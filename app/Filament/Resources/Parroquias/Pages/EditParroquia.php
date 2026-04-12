<?php

namespace App\Filament\Resources\Parroquias\Pages;

use App\Filament\Resources\Parroquias\ParroquiaResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditParroquia extends EditRecord
{
    protected static string $resource = ParroquiaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
