<?php

namespace App\Filament\Resources\WebhookLogs;

use App\Filament\Resources\WebhookLogs\Pages\ManageWebhookLogs;
use App\Models\WebhookLog;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use BackedEnum;
use UnitEnum;

class WebhookLogResource extends Resource
{
    protected static ?string $model = WebhookLog::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-bolt';
    protected static string|UnitEnum|null $navigationGroup = 'Audit & Logs';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')->dateTime()->sortable(),
                TextColumn::make('source')->badge()->searchable(),
                TextColumn::make('event'),
                TextColumn::make('status')->badge()->color(fn (string $state): string => match ($state) {
                    'received' => 'info',
                    'processed' => 'success',
                    'failed' => 'danger',
                }),
                TextColumn::make('processed_at')->dateTime(),
            ])
            ->filters([
                //
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageWebhookLogs::route('/'),
        ];
    }
}
