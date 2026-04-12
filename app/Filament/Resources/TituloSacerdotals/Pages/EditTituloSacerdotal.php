<?php

namespace App\Filament\Resources\TituloSacerdotals\Pages;

use App\Filament\Resources\TituloSacerdotals\TituloSacerdotalResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTituloSacerdotal extends EditRecord
{
    protected static string $resource = TituloSacerdotalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
