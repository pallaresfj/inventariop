<?php

namespace App\Filament\Resources\Restauracions\Pages;

use App\Filament\Resources\Restauracions\RestauracionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRestauracion extends EditRecord
{
    protected static string $resource = RestauracionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
