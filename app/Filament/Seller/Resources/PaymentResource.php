<?php

namespace App\Filament\Seller\Resources;

use App\Filament\Seller\Resources\PaymentResource\Pages;
use App\Models\Payment;
use Filament\Facades\Filament;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    public static function getNavigationGroup(): string
    {
        return __('filament/resources/payment-resource.navigation.group');
    }

    public static function getModelLabel(): string
    {
        return __('filament/resources/payment-resource.navigation.single');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament/resources/payment-resource.navigation.plural');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament/resources/payment-resource.navigation.label');
    }

    public static function form(Form $form): Form
    {
        return \App\Filament\Admin\Resources\PaymentResource::form($form);
    }

    public static function table(Table $table): Table
    {
        return \App\Filament\Admin\Resources\PaymentResource::table($table);
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
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('seller_id', Filament::auth()->id())
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
