<?php

namespace App\Filament\Seller\Resources\OrderResource\Pages;

use App\Filament\Seller\Resources\OrderResource;
use App\Helper\OrderStatus;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [

            'all' => Tab::make()->icon('heroicon-o-squares-2x2'),

            'new' => Tab::make()
                ->icon('heroicon-o-bolt')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', OrderStatus::New)),

            'processing' => Tab::make()
                ->icon('heroicon-o-ellipsis-horizontal-circle')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', OrderStatus::Processing)),

            'delivered' => Tab::make()
                ->icon('heroicon-o-check-circle')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', OrderStatus::Delivered)),

            'cancel' => Tab::make()
                ->icon('heroicon-o-x-circle')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', OrderStatus::Cancelled)),

            'refund' => Tab::make()
                ->icon('heroicon-o-shield-exclamation')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', OrderStatus::Refunded)),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // (OrderResource\Widgets\OrderChart::class)
        ];
    }
}
