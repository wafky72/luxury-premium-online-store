<?php

namespace App\Filament\Resources\CmsPages\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\KeyValue;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class CmsPageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(3)->schema([
                    Section::make('Page Details')->schema([
                        TextInput::make('title')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (string $operation, $state, \Filament\Forms\Set $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),
                        TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true),
                        Select::make('status')
                            ->options(['draft' => 'Draft', 'published' => 'Published'])
                            ->default('draft')
                            ->required(),
                        DateTimePicker::make('published_at'),
                        Toggle::make('is_home'),
                    ])->columnSpan(2),

                    Section::make('SEO')->schema([
                        TextInput::make('meta_title'),
                        TextInput::make('meta_description'),
                        FileUpload::make('og_image')
                            ->image()
                            ->directory('cms/seo'),
                    ])->columnSpan(1),
                ]),

                Section::make('Page Layout')->schema([
                    Repeater::make('sections')
                        ->relationship('allSections')
                        ->schema([
                            Grid::make(2)->schema([
                                Select::make('type')
                                    ->options([
                                        'hero_split' => 'Hero Split',
                                        'product_grid' => 'Product Grid',
                                        'promo_banner' => 'Promo Banner',
                                        'trust_strip' => 'Trust Strip',
                                        'journal_grid' => 'Journal Grid',
                                        'category_cards' => 'Category Cards',
                                    ])
                                    ->required()
                                    ->live(),
                                TextInput::make('label')
                                    ->helperText('Internal name for this section'),
                                Toggle::make('is_active')
                                    ->default(true),
                            ]),
                            KeyValue::make('props_json')
                                ->label('Component Props (JSON)')
                                ->keyLabel('Prop Name')
                                ->valueLabel('Prop Value')
                                ->addActionLabel('Add Prop'),
                        ])
                        ->orderColumn('sort_order')
                        ->defaultItems(1)
                        ->collapsible()
                        ->itemLabel(fn (array $state): ?string => $state['label'] ?? $state['type'] ?? null),
                ]),
            ]);
    }
}
