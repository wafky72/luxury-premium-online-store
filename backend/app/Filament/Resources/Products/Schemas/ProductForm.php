<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\RichEditor;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(3)->schema([
                    Section::make('Product Details')->schema([
                        TextInput::make('name')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (string $operation, $state, \Filament\Forms\Set $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),
                        TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true),
                        TextInput::make('sku')
                            ->label('Base SKU')
                            ->required(),
                        TextInput::make('tag_price')
                            ->numeric()
                            ->label('Tag Price (BDT)')
                            ->helperText('Price to be printed on the barcode tag'),
                        Select::make('category_id')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('collection_id')
                            ->relationship('collection', 'name')
                            ->searchable()
                            ->preload(),
                        RichEditor::make('description')
                            ->columnSpanFull(),
                    ])->columns(2)->columnSpan(2),

                    Section::make('Status & Organization')->schema([
                        Toggle::make('is_active')
                            ->default(true),
                        Toggle::make('is_featured'),
                        TextInput::make('gold_rate_override')
                            ->numeric()
                            ->helperText('Override the daily gold rate just for this product (optional)'),
                    ])->columnSpan(1),
                ]),

                Section::make('Product Variants')->schema([
                    Repeater::make('allVariants')
                        ->relationship('allVariants')
                        ->label('Variants')
                        ->schema([
                            Grid::make(3)->schema([
                                TextInput::make('sku')->required(),
                                TextInput::make('size'),
                                Select::make('karat')
                                    ->options([
                                        '18K' => '18K',
                                        '21K' => '21K',
                                        '22K' => '22K',
                                        '24K' => '24K',
                                    ])->required(),
                                TextInput::make('tag_price')
                                    ->numeric()
                                    ->label('Tag Price (BDT)'),
                            ]),
                            Grid::make(3)->schema([
                                TextInput::make('weight_grams')
                                    ->numeric()
                                    ->step('0.01')
                                    ->required()
                                    ->label('Gold Weight (g)'),
                                TextInput::make('making_charge')
                                    ->numeric()
                                    ->required()
                                    ->default(0),
                                TextInput::make('base_price_override')
                                    ->numeric()
                                    ->helperText('Fixed price (ignores gold weight calculation)'),
                            ]),
                            Grid::make(3)->schema([
                                TextInput::make('stone_type'),
                                TextInput::make('stone_weight')->numeric()->step('0.01'),
                                TextInput::make('stock_qty')->numeric()->required()->default(0),
                            ]),
                        ])
                        ->collapsible()
                        ->itemLabel(fn (array $state): ?string => $state['sku'] ?? null)
                        ->defaultItems(1),
                ]),

                Section::make('Images')->schema([
                    Repeater::make('productImages')
                        ->relationship('productImages')
                        ->label('Images')
                        ->schema([
                            FileUpload::make('url')
                                ->image()
                                ->directory('products')
                                ->required(),
                            Toggle::make('is_primary'),
                        ])
                        ->grid(3)
                        ->collapsible(),
                ]),
            ]);
    }
}
