<?php

namespace App\Filament\Resources\Products\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\Action;
use Filament\Tables;
use Filament\Tables\Table;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sku')
                    ->label('SKU / Barcode')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tag_price')
                    ->label('Tag Price')
                    ->money('BDT')
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('is_active'),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('print_barcode')
                    ->label('Barcode')
                    ->icon('heroicon-o-qr-code')
                    ->color('info')
                    ->form([
                        \Filament\Forms\Components\Checkbox::make('show_brand')->label('Brand Name')->default(true),
                        \Filament\Forms\Components\Checkbox::make('show_name')->label('Product Name')->default(true),
                        \Filament\Forms\Components\Checkbox::make('show_barcode')->label('Barcode (Visual)')->default(true),
                        \Filament\Forms\Components\Checkbox::make('show_sku')->label('SKU Number')->default(true),
                        \Filament\Forms\Components\Checkbox::make('show_price')->label('Tag Price')->default(true),
                    ])
                    ->action(function ($record, array $data) {
                        $url = route('barcode.generate', [
                            'sku' => $record->sku,
                            'name' => $record->name,
                            'price' => $record->tag_price ?? 0,
                            'show_brand' => $data['show_brand'],
                            'show_name' => $data['show_name'],
                            'show_barcode' => $data['show_barcode'],
                            'show_sku' => $data['show_sku'],
                            'show_price' => $data['show_price'],
                        ]);
                        
                        return redirect($url);
                    })
                    ->openUrlInNewTab(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
