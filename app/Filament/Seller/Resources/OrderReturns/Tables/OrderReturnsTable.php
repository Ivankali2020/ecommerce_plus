<?php

namespace App\Filament\Seller\Resources\OrderReturns\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class OrderReturnsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('invoice_number')
                    ->label(__('filament/resources/order-return-resource.table.columns.invoice_number'))
                    ->searchable(),

                TextColumn::make('user.name')
                    ->label(__('filament/resources/order-return-resource.table.columns.user_name'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('status')
                    ->label(__('filament/resources/order-return-resource.table.columns.status'))
                    ->searchable(),
                TextColumn::make('total_refund_amount')
                    ->label(__('filament/resources/order-return-resource.table.columns.total_refund_amount'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('restocking_decision')
                    ->label(__('filament/resources/order-return-resource.table.columns.restocking_decision'))
                    ->badge(),
                IconColumn::make('should_restock')
                    ->label(__('filament/resources/order-return-resource.table.columns.should_restock'))
                    ->boolean(),
                TextColumn::make('requested_at')
                    ->label(__('filament/resources/order-return-resource.table.columns.requested_at'))
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                TextColumn::make('approved_at')
                    ->label(__('filament/resources/order-return-resource.table.columns.approved_at'))
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                TextColumn::make('completed_at')
                    ->label(__('filament/resources/order-return-resource.table.columns.completed_at'))
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                TextColumn::make('deleted_at')
                    ->label(__('filament/resources/order-return-resource.table.columns.deleted_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label(__('filament/resources/order-return-resource.table.columns.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('filament/resources/order-return-resource.table.columns.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make()
                    ->label(__('filament/resources/order-return-resource.table.filters.trashed')),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label(__('filament/resources/order-return-resource.table.actions.view')),
                EditAction::make()
                    ->label(__('filament/resources/order-return-resource.table.actions.edit')),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label(__('filament/resources/order-return-resource.table.actions.delete')),
                    ForceDeleteBulkAction::make()
                        ->label(__('filament/resources/order-return-resource.table.actions.force_delete')),
                    RestoreBulkAction::make()
                        ->label(__('filament/resources/order-return-resource.table.actions.restore')),
                ]),
            ]);
    }
}
