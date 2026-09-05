<?php

namespace App\Filament\Resources\GoldRates\Pages;

use App\Filament\Resources\GoldRates\GoldRateResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditGoldRate extends EditRecord
{
    protected static string $resource = GoldRateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
