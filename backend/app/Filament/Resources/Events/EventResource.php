<?php

namespace App\Filament\Resources\Events;

use App\Filament\Resources\Events\Pages\ManageEvents;
use App\Models\Event;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Table;
use BackedEnum;
use UnitEnum;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-signal';
    protected static string|UnitEnum|null $navigationGroup = 'Audit & Logs';
    protected static ?string $navigationLabel = 'Tracking Events';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')->dateTime()->sortable(),
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('source')->badge(),
                TextColumn::make('user.email')->label('User'),
                IconColumn::make('synced_to_fb')->boolean()->label('FB Sync'),
                TextColumn::make('ip_address')->label('IP'),
            ])
            ->filters([
                //
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageEvents::route('/'),
        ];
    }
}
