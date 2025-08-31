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
                Section::make('Order Return Details')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('order.invoice_number')
                                    ->label('Order Number')
                                    ->weight(FontWeight::SemiBold)
                                    ->color('primary'),

                                TextEntry::make('invoice_number')
                                    ->label('Return Number')
                                    ->weight(FontWeight::SemiBold)
                                    ->color('warning'),

                                TextEntry::make('status')
                                    ->label('Status')
                                    ->badge()
                                    ->color(ReturnStatus::class),

                                TextEntry::make('total_refund_amount')
                                    ->label('Total Refund Amount')
                                    ->formatStateUsing(fn ($state) => number_format($state, 2).' MMK')
                                    ->weight(FontWeight::Bold)
                                    ->color('success'),
                            ]),

                        Grid::make(1)
                            ->schema([
                                TextEntry::make('return_reason')
                                    ->label('Return Reason')
                                    ->columnSpanFull(),

                                TextEntry::make('notes')
                                    ->label('Notes')
                                    ->columnSpanFull()
                                    ->placeholder('No notes provided'),
                            ]),
                    ])
                    ->icon('heroicon-o-document-text')
                    ->collapsible(),

                Section::make('Customer Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('order.user.name')
                                    ->label('Customer Name')
                                    ->weight(FontWeight::SemiBold),

                                TextEntry::make('order.user.email')
                                    ->label('Email')
                                    ->copyable()
                                    ->copyMessage('Email copied!')
                                    ->color('primary'),

                                TextEntry::make('order.created_at')
                                    ->label('Order Date')
                                    ->dateTime(),

                                TextEntry::make('created_at')
                                    ->label('Return Date')
                                    ->dateTime(),
                            ]),

                        Grid::make(3)
                            ->schema([
                                TextEntry::make('order.sub_total')
                                    ->label('Original Order Total')
                                    ->formatStateUsing(fn ($state) => number_format($state, 2).' MMK')
                                    ->weight(FontWeight::SemiBold),

                                TextEntry::make('total_refund_amount')
                                    ->label('Total Refund')
                                    ->formatStateUsing(fn ($state) => number_format($state, 2).' MMK')
                                    ->weight(FontWeight::Bold)
                                    ->color('success'),

                                TextEntry::make('returnProducts')
                                    ->label('Items Returned')
                                    ->state(fn ($record) => $record->returnProducts->sum('quantity_approved'))
                                    ->badge()
                                    ->color('info'),
                            ]),
                    ])
                    ->icon('heroicon-o-user')
                    ->collapsible(),

                Section::make('Return Products')
                    ->schema([
                        RepeatableEntry::make('returnProducts')
                            ->schema([
                                Flex::make([
                                    TextEntry::make('orderProduct.product.name')
                                        ->label('Product Name')
                                        ->weight(FontWeight::SemiBold)
                                        ->columnSpan(2),

                                    TextEntry::make('quantity_requested')
                                        ->label('Quantity')
                                        ->badge()
                                        ->color('info'),

                                    TextEntry::make('total_refund_amount')
                                        ->label('Refund Amount')
                                        ->formatStateUsing(fn ($state) => number_format($state, 2).' MMK')
                                        ->weight(FontWeight::SemiBold)
                                        ->color('success'),

                                    TextEntry::make('orderProduct.product.sku')
                                        ->label('SKU')
                                        ->color('gray'),

                                    TextEntry::make('unit_refund_amount')
                                        ->label('Original Price')
                                        ->formatStateUsing(fn ($state) => number_format($state, 2).' MMK'),

                                    IconEntry::make('orderProduct.product.is_return')
                                        ->label('Returnable')
                                        ->boolean()
                                        ->trueIcon('heroicon-o-check-circle')
                                        ->falseIcon('heroicon-o-x-circle')
                                        ->trueColor('success')
                                        ->falseColor('danger'),
                                ]),

                                TextEntry::make('return_reason')
                                    ->label('Return Reason')
                                    ->columnSpanFull()
                                    ->hidden(fn ($state) => blank($state))
                                    ->placeholder('No specific reason provided'),
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
