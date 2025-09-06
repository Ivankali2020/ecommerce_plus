<?php

namespace App\Filament\Seller\Resources\Printers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PrintersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),

                TextColumn::make('connection_type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'network' => 'success',
                        'usb' => 'info',
                    }),

                TextColumn::make('connection_string')
                    ->label('Connection')
                    ->getStateUsing(function ($record) {
                        return match ($record->connection_type) {
                            'network' => "{$record->ip_address}:{$record->port}",
                            'usb' => $record->usb_path,
                            default => 'N/A',
                        };
                    }),

                IconColumn::make('is_default')
                    ->boolean(),

                IconColumn::make('is_active')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
