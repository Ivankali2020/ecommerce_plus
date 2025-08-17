<?php

namespace App\Filament\Seller\Resources\OrderProducts;

use App\Filament\Seller\Resources\OrderProductResource\Pages;
use App\Filament\Seller\Resources\OrderProducts\Pages\ListOrderProducts;
use App\Helper\CheckFilamentSellerUser;
use App\Models\OrderProduct;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderProductResource extends Resource
{
    protected static ?string $model = OrderProduct::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Order Section';

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

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->groups([
                'order.invoice_number',
                'product.name',
                Group::make('created_at')
                    ->label(__('filament/resources/order-product-resource.columns.created_at'))
                    ->date(),
            ])
            ->groupingSettingsInDropdownOnDesktop()
            ->columns([
                TextColumn::make('product.name')
                    ->label(__('filament/resources/order-product-resource.columns.product.name'))
                    ->searchable()
                    ->description(fn (OrderProduct $orderProduct) => $orderProduct->product->category->name),

                TextColumn::make('attributes')
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

                TextColumn::make('original_buy_price')
                    ->label(__('filament/resources/order-product-resource.columns.original_buy_price'))
                    ->alignEnd(),
                // ->description(fn(OrderProduct $orderProduct) => $orderProduct->original_price),

                TextColumn::make('price')
                    ->label(__('filament/resources/order-product-resource.columns.selling_price'))
                    ->alignEnd(),

                TextColumn::make('discount_price')
                    ->label(__('filament/resources/order-product-resource.columns.discount_price'))
                    ->alignEnd()
                    ->description(fn (OrderProduct $orderProduct) => $orderProduct->discount.' %'),

                TextColumn::make('quantity')
                    ->label(__('filament/resources/order-product-resource.columns.quantity'))
                    ->alignCenter(),

                TextColumn::make('sub_price')
                    ->label(__('filament/resources/order-product-resource.columns.sub_price'))
                    ->alignEnd()
                    ->summarize(
                        Sum::make('sub_price')
                            ->money('mmk')
                            ->label(__('filament/resources/order-product-resource.columns.total_amount'))
                    ),

                TextColumn::make('profit')
                    ->label(__('filament/resources/order-product-resource.columns.profit_loss'))
                    ->alignEnd()
                    ->summarize(
                        Sum::make('profit')
                            ->money('mmk')
                            ->label(__('filament/resources/order-product-resource.columns.profit_loss'))
                    ),
            ])
            ->defaultSort('id', 'desc')
            ->filters([

                Filter::make('created_at')
                    ->schema([
                        DatePicker::make('created_from')
                            ->label(__('filament/resources/order-product-resource.filters.created_from')),
                        DatePicker::make('created_until')
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

                SelectFilter::make('product_id')
                    ->label(__('filament/resources/order-product-resource.filters.product'))
                    ->relationship('product', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),

                Filter::make('today')
                    ->label('Today')
                    ->query(fn (Builder $query) => $query->whereDate('created_at', today()))
                    ->toggle(),

            ], layout: FiltersLayout::AboveContent)
            ->recordActions([
                EditAction::make()
                    ->label(__('filament/resources/order-product-resource.actions.edit')),
            ])
            // ->filtersFormWidth(MaxWidth::Full)
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
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
            'index' => ListOrderProducts::route('/'),
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
