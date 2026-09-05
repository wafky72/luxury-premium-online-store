<?php

namespace App\Filament\Resources\Payments\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PaymentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('order_id')
                    ->relationship('order', 'id')
                    ->required(),
                Select::make('gateway')
                    ->options(['cod' => 'Cod', 'bkash' => 'Bkash', 'nagad' => 'Nagad', 'sslcommerz' => 'Sslcommerz'])
                    ->required(),
                TextInput::make('transaction_id'),
                TextInput::make('gateway_order_id'),
                TextInput::make('amount')
                    ->required()
                    ->numeric(),
                Select::make('status')
                    ->options([
            'pending' => 'Pending',
            'completed' => 'Completed',
            'failed' => 'Failed',
            'refunded' => 'Refunded',
        ])
                    ->default('pending')
                    ->required(),
                TextInput::make('gateway_response'),
                TextInput::make('payment_method_detail'),
                DateTimePicker::make('verified_at'),
            ]);
    }
}
