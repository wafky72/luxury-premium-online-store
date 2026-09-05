<?php

namespace App\Filament\Resources\Orders\Tables;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use App\Models\Order;
use App\Services\NotificationService;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_number')
                    ->searchable()
                    ->weight('bold'),
                TextColumn::make('user.name')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('coupon.id')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('recipient_name')
                    ->searchable(),
                TextColumn::make('recipient_phone')
                    ->searchable(),
                TextColumn::make('shipping_city')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('shipping_district')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('subtotal')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('shipping_fee')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('coupon_discount')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('vat')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('total')
                    ->numeric()
                    ->sortable()
                    ->money('bdt'),
                TextColumn::make('payment_method')
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('payment_status')
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('advance_paid')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('courier')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('courier_service')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('courier_charge')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('fraud_score')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('source')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('tenant_id')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    Action::make('download_invoice')
                        ->label('Download Invoice')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('success')
                        ->url(fn ($record) => route('orders.invoice.download', $record))
                        ->openUrlInNewTab(),
                    Action::make('send_sms')
                        ->label('Send SMS')
                        ->icon('heroicon-o-chat-bubble-left-ellipsis')
                        ->color('info')
                        ->form([
                            \Filament\Forms\Components\Textarea::make('message')
                                ->label('Message')
                                ->required()
                                ->default(fn ($record) => "Dear {$record->recipient_name}, your order #{$record->order_number} status is currently: {$record->status}."),
                        ])
                        ->action(function ($record, array $data) {
                            $phone = $record->recipient_phone;
                            $message = $data['message'];
                            \App\Services\NotificationService::sendSms($phone, $message);
                            \Filament\Notifications\Notification::make()
                                ->title('SMS Sent Successfully')
                                ->success()
                                ->send();
                        }),
                    Action::make('add_to_delivery')
                        ->label('Add to Delivery')
                        ->icon('heroicon-o-truck')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->action(function ($record) {
                            // Integrate with Steadfast or Pathao here
                            $record->update(['status' => 'shipped']);
                            \Filament\Notifications\Notification::make()
                                ->title('Order added to delivery queue')
                                ->success()
                                ->send();
                        }),
                    \Filament\Actions\DeleteAction::make(),
                ])
                ->label('Order Actions')
                ->icon('heroicon-m-ellipsis-vertical')
                ->color('primary'),
            ])
            ->toolbarActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                    \Filament\Actions\ForceDeleteBulkAction::make(),
                    \Filament\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }
}
