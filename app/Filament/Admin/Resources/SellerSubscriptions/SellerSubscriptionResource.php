<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\SellerSubscriptionResource\Pages\CreateSellerSubscription;
use App\Filament\Admin\Resources\SellerSubscriptionResource\Pages\EditSellerSubscription;
use App\Filament\Admin\Resources\SellerSubscriptionResource\Pages\ListSellerSubscriptions;
use App\Models\SellerSubscription;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SellerSubscriptionResource extends Resource
{
    protected static ?string $model = SellerSubscription::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('seller_id')
                    ->required()
                    ->relationship('seller', 'name'),
                Select::make('subscription_plan_id')
                    ->required()
                    ->relationship('subscriptionPlan', 'name'),
                DateTimePicker::make('started_at')
                    ->required(),
                DateTimePicker::make('expired_at')
                    ->required(),
                TextInput::make('price_paid')
                    ->required()
                    ->numeric(),

                ToggleButtons::make('payment_status')
                    ->required()
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),

                TextInput::make('payment_method')
                    ->maxLength(255),

                SpatieMediaLibraryFileUpload::make('payment_slips')
                    ->label('Payment Slip')
                    ->image()
                    ->required()
                    ->maxSize(5120) // 5MB
                    ->directory('subscription-payments'),

                Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('seller.name')
                    ->description(fn (SellerSubscription $record) => $record->subscriptionPlan->name)
                    ->sortable(),

                TextColumn::make('started_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('expired_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('price_paid')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('payment_status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    })
                    ->searchable(),
                IconColumn::make('has_payment_slip')
                    ->label(__('subscription.payment_slip'))
                    ->boolean()
                    ->getStateUsing(fn (SellerSubscription $record): bool => $record->getMedia('payment_slips')->isNotEmpty())
                    ->tooltip(
                        fn (SellerSubscription $record): string => $record->getMedia('payment_slips')->isNotEmpty()
                            ? __('subscription.payment_slip_uploaded')
                            : __('subscription.no_payment_slip')
                    ),
                TextColumn::make('payment_method')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('view_payment_slip')
                    ->label(__('subscription.view_payment_slip'))
                    ->icon('heroicon-o-photo')
                    ->color('info')
                    ->modalHeading(__('subscription.payment_slip'))
                    ->modalContent(function (SellerSubscription $record) {
                        $mediaItems = $record->getMedia('payment_slips');

                        if ($mediaItems->isEmpty()) {
                            return view('filament.components.no-payment-slip');
                        }

                        return view('filament.components.payment-slip-viewer', [
                            'mediaItems' => $mediaItems,
                        ]);
                    })
                    ->modalWidth('4xl')
                // ->visible(fn (SellerSubscription $record) => $record->getMedia('payment_slips')->isNotEmpty())
                ,

                Action::make('confirm')->button()
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
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
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
            'index' => ListSellerSubscriptions::route('/'),
            'create' => CreateSellerSubscription::route('/create'),
            'edit' => EditSellerSubscription::route('/{record}/edit'),
        ];
    }
}
