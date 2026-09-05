<?php

namespace App\Filament\Resources\GoldRates\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Card;
use Filament\Schemas\Schema;

class GoldRateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Card::make()->schema([
                    Select::make('karat')
                        ->options([
                            '18K' => '18 Karat',
                            '21K' => '21 Karat',
                            '22K' => '22 Karat',
                            '24K' => '24 Karat',
                        ])
                        ->required()
                        ->label('Gold Purity (Karat)'),
                    
                    TextInput::make('price_per_gram')
                        ->numeric()
                        ->required()
                        ->prefix('BDT')
                        ->label('Price per Gram'),

                    DateTimePicker::make('effective_date')
                        ->required()
                        ->default(now())
                        ->label('Effective From'),
                ])->columns(2)
            ]);
    }
}
