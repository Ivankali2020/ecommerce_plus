<?php

namespace App\Filament\Seller\Resources;

use App\Filament\Seller\Resources\OrderProductResource\Pages;
use App\Helper\CheckFilamentSellerUser;
use App\Models\OrderProduct;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderProductResource extends Resource
{
    protected static ?string $model = OrderProduct::class;

    protected static ?string $navigationGroup = 'Order Section';

    public static function getNavigationLabel(): string
    {
        return __('filament/resources/order-product-resource.navigation.label');
    }

    public static function getModelLabel(): string
    {
        return __('filament/resources/order-product-resource.navigation.single');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament/resources/order-product-resource.navigation.plural');
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    //    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->groups([
                'order.invoice_number',
                'product.name',
                Tables\Grouping\Group::make('created_at')
                    ->label(__('filament/resources/order-product-resource.columns.created_at'))
                    ->date(),
            ])
            ->groupingSettingsInDropdownOnDesktop()
            ->columns([
                Tables\Columns\TextColumn::make('product.name')
                    ->label(__('filament/resources/order-product-resource.columns.product.name'))
                    ->searchable()
                    ->description(fn (OrderProduct $orderProduct) => $orderProduct->product->category->name),

                Tables\Columns\TextColumn::make('attributes')
                    ->label(__('filament/resources/order-product-resource.columns.attributes'))
                    ->getStateUsing(function ($record) {
                        $data = '';
                        foreach ($record->attributes as $attribute) {
                            $array = implode(' - ', collect($attribute['value'] ?? [])->toArray());
                            $data .= "<span class='border text-xs text-orange-500 dark:text-orange-400 border-gray-500 dark:border-gray-700 py-1  px-2 rounded '>".$array.' </span>';
                        }

                        return $data;
                    })->color('orange')
                    ->html()
                    ->formatStateUsing(fn ($state) => $state),

                Tables\Columns\TextColumn::make('original_buy_price')
                    ->label(__('filament/resources/order-product-resource.columns.original_buy_price'))
                    ->alignEnd(),
                // ->description(fn(OrderProduct $orderProduct) => $orderProduct->original_price),

                Tables\Columns\TextColumn::make('price')
                    ->label(__('filament/resources/order-product-resource.columns.selling_price'))
                    ->alignEnd(),

                Tables\Columns\TextColumn::make('discount_price')
                    ->label(__('filament/resources/order-product-resource.columns.discount_price'))
                    ->alignEnd()
                    ->description(fn (OrderProduct $orderProduct) => $orderProduct->discount.' %'),

                Tables\Columns\TextColumn::make('quantity')
                    ->label(__('filament/resources/order-product-resource.columns.quantity'))
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('sub_price')
                    ->label(__('filament/resources/order-product-resource.columns.sub_price'))
                    ->alignEnd()
                    ->summarize(
                        Tables\Columns\Summarizers\Sum::make('sub_price')
                            ->money('mmk')
                            ->label(__('filament/resources/order-product-resource.columns.total_amount'))
                    ),

                Tables\Columns\TextColumn::make('profit')
                    ->label(__('filament/resources/order-product-resource.columns.profit_loss'))
                    ->alignEnd()
                    ->summarize(
                        Tables\Columns\Summarizers\Sum::make('profit')
                            ->money('mmk')
                            ->label(__('filament/resources/order-product-resource.columns.profit_loss'))
                    ),
                ])
            ->defaultSort('id','desc')
            ->filters([

                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label(__('filament/resources/order-product-resource.filters.created_from')),
                        Forms\Components\DatePicker::make('created_until')
                            ->label(__('filament/resources/order-product-resource.filters.created_until')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })->columnSpan(2),

                Tables\Filters\SelectFilter::make('product_id')
                    ->label(__('filament/resources/order-product-resource.filters.product'))
                    ->relationship('product', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),

                    Tables\Filters\Filter::make('today')
                    ->label('Today')
                    ->query(fn (Builder $query) => $query->whereDate('created_at', today()))
                    ->toggle(),

                ],layout: FiltersLayout::AboveContent)
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label(__('filament/resources/order-product-resource.actions.edit')),
            ])
            // ->filtersFormWidth(MaxWidth::Full)
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label(__('filament/resources/order-product-resource.actions.delete')),
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
            'index' => Pages\ListOrderProducts::route('/'),
            //            'create' => Pages\CreateOrderProduct::route('/create'),
            //            'edit' => Pages\EditOrderProduct::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereRelation('order', 'seller_id', CheckFilamentSellerUser::getSellerId())
            ->withoutGlobalScopes([
                //                SoftDeletingScope::class,
            ]);
    }
}
