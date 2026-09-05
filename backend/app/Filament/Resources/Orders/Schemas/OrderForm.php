<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(3)->schema([
                    Section::make('Order Information')->schema([
                        TextInput::make('order_number')
                            ->placeholder('TC-' . date('Y') . '-XXXXX')
                            ->helperText('Leave blank to auto-generate'),
                        Select::make('user_id')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->required(),
                        Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'confirmed' => 'Confirmed',
                                'processing' => 'Processing',
                                'shipped' => 'Shipped',
                                'delivered' => 'Delivered',
                                'cancelled' => 'Cancelled',
                                'returned' => 'Returned',
                            ])
                            ->required(),
                        Select::make('payment_status')
                            ->options([
                                'pending' => 'Pending',
                                'paid' => 'Paid',
                                'failed' => 'Failed',
                                'refunded' => 'Refunded',
                            ])
                            ->required(),
                        TextInput::make('payment_method')->disabled(),
                        TextInput::make('fraud_score')
                            ->numeric()
                            ->disabled()
                            ->helperText('Score calculated via BDCouriers AI'),
                    ])->columns(2)->columnSpan(2),

                    Section::make('Financials')->schema([
                        TextInput::make('subtotal')->disabled()->numeric(),
                        TextInput::make('shipping_fee')->disabled()->numeric(),
                        TextInput::make('discount')->disabled()->numeric(),
                        TextInput::make('vat')->disabled()->numeric(),
                        TextInput::make('total')
                            ->disabled()
                            ->numeric()
                            ->extraInputAttributes(['class' => 'font-bold text-lg']),
                    ])->columnSpan(1),
                ]),

                Section::make('Order Items')->schema([
                    Repeater::make('items')
                        ->relationship()
                        ->schema([
                            Grid::make(4)->schema([
                                TextInput::make('name')->disabled()->columnSpan(2),
                                TextInput::make('sku')->disabled(),
                                TextInput::make('qty')->disabled()->numeric(),
                                TextInput::make('karat')->disabled(),
                                TextInput::make('weight_grams')->disabled()->numeric(),
                                TextInput::make('unit_price')->disabled()->numeric(),
                                TextInput::make('total_price')->disabled()->numeric(),
                            ])
                        ])
                        ->defaultItems(1),
                ]),

                Section::make('Timeline & Notes')->schema([
                    Textarea::make('notes')->rows(3),
                    // Timeline would ideally be managed via relationship or a custom Livewire component
                ]),
            ]);
    }
}
