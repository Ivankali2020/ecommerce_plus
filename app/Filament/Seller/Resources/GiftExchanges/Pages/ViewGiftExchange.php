<?php

namespace App\Filament\Seller\Resources\GiftExchanges\Pages;

use App\Filament\Seller\Resources\GiftExchanges\GiftExchangeResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewGiftExchange extends ViewRecord
{
    protected static string $resource = GiftExchangeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            Action::make('mark_processed')
                ->label('Mark as Processed')
                ->icon('heroicon-m-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->action(function () {
                    $this->record->update([
                        'status' => 'processing',
                        'processed_at' => now(),
                    ]);

                    $this->redirect($this->getResource()::getUrl('view', ['record' => $this->record]));
                })
                ->visible(fn () => $this->record->status === 'pending'),
            Action::make('mark_shipped')
                ->label('Mark as Shipped')
                ->icon('heroicon-m-truck')
                ->color('warning')
                ->requiresConfirmation()
                ->action(function () {
                    $this->record->update([
                        'status' => 'shipped',
                        'shipped_at' => now(),
                    ]);

                    $this->redirect($this->getResource()::getUrl('view', ['record' => $this->record]));
                })
                ->visible(fn () => in_array($this->record->status, ['processing', 'pending'])),
            Action::make('mark_delivered')
                ->label('Mark as Delivered')
                ->icon('heroicon-m-check-badge')
                ->color('success')
                ->requiresConfirmation()
                ->action(function () {
                    $this->record->update([
                        'status' => 'delivered',
                        'delivered_at' => now(),
                    ]);

                    $this->redirect($this->getResource()::getUrl('view', ['record' => $this->record]));
                })
                ->visible(fn () => $this->record->status === 'shipped'),
        ];
    }
}
