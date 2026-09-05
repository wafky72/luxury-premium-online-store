<?php

namespace App\Filament\Resources\NotificationLogs;

use App\Filament\Resources\NotificationLogs\Pages\ManageNotificationLogs;
use App\Models\NotificationLog;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use BackedEnum;
use UnitEnum;

class NotificationLogResource extends Resource
{
    protected static ?string $model = NotificationLog::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static string|UnitEnum|null $navigationGroup = 'Audit & Logs';
    protected static ?string $navigationLabel = 'SMS & Email Logs';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')->dateTime()->sortable(),
                TextColumn::make('channel')->badge(),
                TextColumn::make('recipient')->searchable(),
                TextColumn::make('template'),
                TextColumn::make('status')->badge()->color(fn (string $state): string => match ($state) {
                    'sent' => 'success',
                    'pending' => 'info',
                    'failed' => 'danger',
                }),
                TextColumn::make('error')->limit(20)->tooltip(fn ($record) => $record->error),
            ])
            ->filters([
                //
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageNotificationLogs::route('/'),
        ];
    }
}
