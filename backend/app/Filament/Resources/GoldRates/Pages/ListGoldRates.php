<?php

namespace App\Filament\Resources\GoldRates\Pages;

use App\Filament\Resources\GoldRates\GoldRateResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListGoldRates extends ListRecords
{
    protected static string $resource = GoldRateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
