<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\SellerSubscriptionResource\Pages;
use App\Filament\Admin\Resources\SellerSubscriptionResource\RelationManagers;
use App\Models\SellerSubscription;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SellerSubscriptionResource extends Resource
{
    protected static ?string $model = SellerSubscription::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('seller_id')
                    ->required()
                    ->relationship('seller', 'name'),
                Forms\Components\Select::make('subscription_plan_id')
                    ->required()
                    ->relationship('subscriptionPlan', 'name'),
                Forms\Components\DateTimePicker::make('started_at')
                    ->required(),
                Forms\Components\DateTimePicker::make('expired_at')
                    ->required(),
                Forms\Components\TextInput::make('price_paid')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('payment_status')
                    ->required()
                    ->maxLength(255)
                    ->default('pending'),
                Forms\Components\TextInput::make('payment_method')
                    ->maxLength(255),
                Forms\Components\Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('seller.name')
                    ->description(fn (SellerSubscription $record) => $record->subscriptionPlan->name)
                    ->sortable(),
            
                Tables\Columns\TextColumn::make('started_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('expired_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('price_paid')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('payment_method')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('confirm')->button()
                    ->label('Confirm')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Confirm Subscription')
                    ->modalDescription('Are you sure you want to confirm this subscription? This will update the seller\'s subscription status.')
                    ->action(function (SellerSubscription $record) {
                        // Update the seller's subscription details
                        $seller = $record->seller;
                        $seller->subscription_plan_id = $record->subscription_plan_id;
                        $seller->subscription_started_at = $record->started_at;
                        $seller->subscription_expired_at = $record->expired_at;
                        $seller->subscription_status = 'active';
                        $seller->save();
                        
                        // Update the subscription status
                        $record->payment_status = 'approved';
                        $record->save();
                        
                       Notification::make()
                            ->title('Subscription Confirmed')
                            ->success()
                            ->send();
                    })
                    ->visible(fn (SellerSubscription $record) => $record->payment_status === 'pending'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListSellerSubscriptions::route('/'),
            'create' => Pages\CreateSellerSubscription::route('/create'),
            'edit' => Pages\EditSellerSubscription::route('/{record}/edit'),
        ];
    }
}
