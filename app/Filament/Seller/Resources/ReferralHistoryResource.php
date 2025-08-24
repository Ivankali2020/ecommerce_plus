<?php

namespace App\Filament\Seller\Resources;

use App\Filament\Seller\Resources\ReferralHistoryResource\Pages;
use App\Models\ReferralHistory;
use BackedEnum;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;

class ReferralHistoryResource extends Resource
{
    protected static ?string $model = ReferralHistory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ArrowPath;

    protected static string|\UnitEnum|null $navigationGroup = 'Loyalty Program';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('referrer.name')
                    ->label(__('filament/resources/referral-history-resource.columns.referrer'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('referred.name')
                    ->label(__('filament/resources/referral-history-resource.columns.referred'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('points_awarded')
                    ->label(__('filament/resources/referral-history-resource.columns.points'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('verified_at')
                    ->label(__('filament/resources/referral-history-resource.columns.verified_at'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('filament/resources/referral-history-resource.columns.created_at'))
                    ->sortable()
                    ->searchable(),

            ])
            ->filters([
                //
            ])
            ->recordActions([
                Actions\EditAction::make(),
            ])
            ->toolbarActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReferralHistories::route('/'),
            'create' => Pages\CreateReferralHistory::route('/create'),
            'edit' => Pages\EditReferralHistory::route('/{record}/edit'),
        ];
    }
}
