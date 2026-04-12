<?php

namespace App\Filament\Resources\Arciprestazgos\Pages;

use App\Filament\Resources\Arciprestazgos\ArciprestazgoResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditArciprestazgo extends EditRecord
{
    protected static string $resource = ArciprestazgoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
