<?php

namespace App\Filament\Seller\Resources\OrderReturns\Schemas;

use App\Helper\ReturnStatus;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;

class OrderReturnInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('filament/resources/order-return-resource.infolist.sections.order_return_details'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('order.invoice_number')
                                    ->label(__('filament/resources/order-return-resource.infolist.entries.order_number'))
                                    ->weight(FontWeight::SemiBold)
                                    ->color('primary'),

                                TextEntry::make('invoice_number')
                                    ->label(__('filament/resources/order-return-resource.infolist.entries.return_number'))
                                    ->weight(FontWeight::SemiBold)
                                    ->color('warning'),

                                TextEntry::make('status')
                                    ->label(__('filament/resources/order-return-resource.infolist.entries.status'))
                                    ->badge()
                                    ->color(ReturnStatus::class),

                                TextEntry::make('total_refund_amount')
                                    ->label(__('filament/resources/order-return-resource.infolist.entries.total_refund_amount'))
                                    ->formatStateUsing(fn($state) => number_format($state, 2) . ' MMK')
                                    ->weight(FontWeight::Bold)
                                    ->color('success'),
                            ]),

                        Grid::make(1)
                            ->schema([
                                TextEntry::make('return_reason')
                                    ->label(__('filament/resources/order-return-resource.infolist.entries.return_reason'))
                                    ->columnSpanFull(),

                                TextEntry::make('notes')
                                    ->label(__('filament/resources/order-return-resource.infolist.entries.notes'))
                                    ->columnSpanFull()
                                    ->placeholder(__('filament/resources/order-return-resource.infolist.placeholders.no_notes')),
                            ]),
                    ])
                    ->icon('heroicon-o-document-text')
                    ->collapsible(),

                Section::make(__('filament/resources/order-return-resource.infolist.sections.customer_information'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('order.user.name')
                                    ->label(__('filament/resources/order-return-resource.infolist.entries.customer_name'))
                                    ->weight(FontWeight::SemiBold),

                                TextEntry::make('order.user.email')
                                    ->label(__('filament/resources/order-return-resource.infolist.entries.email'))
                                    ->copyable()
                                    ->copyMessage(__('filament/resources/order-return-resource.infolist.messages.email_copied'))
                                    ->color('primary'),

                                TextEntry::make('order.created_at')
                                    ->label(__('filament/resources/order-return-resource.infolist.entries.order_date'))
                                    ->dateTime(),

                                TextEntry::make('created_at')
                                    ->label(__('filament/resources/order-return-resource.infolist.entries.return_date'))
                                    ->dateTime(),
                            ]),

                        Grid::make(3)
                            ->schema([
                                TextEntry::make('order.sub_total')
                                    ->label(__('filament/resources/order-return-resource.infolist.entries.original_order_total'))
                                    ->formatStateUsing(fn($state) => number_format($state, 2) . ' MMK')
                                    ->weight(FontWeight::SemiBold),

                                TextEntry::make('total_refund_amount')
                                    ->label(__('filament/resources/order-return-resource.infolist.entries.total_refund'))
                                    ->formatStateUsing(fn($state) => number_format($state, 2) . ' MMK')
                                    ->weight(FontWeight::Bold)
                                    ->color('success'),

                                TextEntry::make('returnProducts')
                                    ->label(__('filament/resources/order-return-resource.infolist.entries.items_returned'))
                                    ->state(fn($record) => $record->returnProducts->sum('quantity_approved'))
                                    ->badge()
                                    ->color('info'),
                            ]),
                    ])
                    ->icon('heroicon-o-user')
                    ->collapsible(),

                Section::make(__('filament/resources/order-return-resource.infolist.sections.return_products'))
                    ->schema([
                        RepeatableEntry::make('returnProducts')
                            ->schema([
                                Flex::make([
                                    TextEntry::make('orderProduct.product.name')
                                        ->label(__('filament/resources/order-return-resource.infolist.entries.product_name'))
                                        ->weight(FontWeight::SemiBold)
                                        ->columnSpan(2),

                                    TextEntry::make('quantity_requested')
                                        ->label(__('filament/resources/order-return-resource.infolist.entries.quantity'))
                                        ->badge()
                                        ->color('info'),

                                    TextEntry::make('total_refund_amount')
                                        ->label(__('filament/resources/order-return-resource.infolist.entries.refund_amount'))
                                        ->formatStateUsing(fn($state) => number_format($state, 2) . ' MMK')
                                        ->weight(FontWeight::SemiBold)
                                        ->color('success'),

                                    TextEntry::make('orderProduct.product.sku')
                                        ->label(__('filament/resources/order-return-resource.infolist.entries.sku'))
                                        ->color('gray'),

                                    TextEntry::make('unit_refund_amount')
                                        ->label(__('filament/resources/order-return-resource.infolist.entries.original_price'))
                                        ->formatStateUsing(fn($state) => number_format($state, 2) . ' MMK'),

                                    IconEntry::make('orderProduct.product.is_return')
                                        ->label(__('filament/resources/order-return-resource.infolist.entries.returnable'))
                                        ->boolean()
                                        ->trueIcon('heroicon-o-check-circle')
                                        ->falseIcon('heroicon-o-x-circle')
                                        ->trueColor('success')
                                        ->falseColor('danger'),
                                ]),

                                TextEntry::make('return_reason')
                                    ->label(__('filament/resources/order-return-resource.infolist.entries.return_reason'))
                                    ->columnSpanFull()
                                    ->hidden(fn($state) => blank($state))
                                    ->placeholder(__('filament/resources/order-return-resource.infolist.placeholders.no_specific_reason_provided')),
                            ])
                            ->columns(1)
                            ->contained(false),
                    ])
                    ->columnSpanFull()
                    ->icon('heroicon-o-shopping-bag')
                    ->collapsible(),
            ]);
    }
}
