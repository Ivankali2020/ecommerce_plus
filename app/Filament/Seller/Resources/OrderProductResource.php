<?php

namespace App\Filament\Seller\Resources;

use App\Filament\Seller\Resources\OrderProductResource\Pages;
use App\Filament\Seller\Resources\OrderProductResource\RelationManagers;
use App\Models\Order;
use App\Models\OrderProduct;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use PHPUnit\Metadata\Group;

class OrderProductResource extends Resource
{
    protected static ?string $model = OrderProduct::class;

    protected static ?string $navigationGroup = 'Order Section';

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
            ->groups(['order.invoice_number','product.name', Tables\Grouping\Group::make('created_at')->date()])
            ->groupingSettingsInDropdownOnDesktop()
            ->columns([
                Tables\Columns\TextColumn::make('product.name')
                    ->searchable()
                    ->description(fn(OrderProduct $orderProduct)=>$orderProduct->product->category->name),

                Tables\Columns\TextColumn::make('attributes')
                    ->getStateUsing(function($record){
                        $data = '';
                        foreach ($record->attributes as $attribute){
                            $array = implode(' - ',collect($attribute['value'])->toArray());
                            $data .= "<span class='border text-sm text-orange-500 dark:text-orange-400 border-gray-500 dark:border-gray-700 py-1  px-2 rounded '>".$array." </span>";
                        }
                        return $data;
                    })->color('orange')
                    ->html()
                    ->formatStateUsing(fn($state)=> $state),

                Tables\Columns\TextColumn::make('original_buy_price')
                    ->alignEnd()
                    ->description(fn(OrderProduct $orderProduct) =>  $orderProduct->original_price),

                Tables\Columns\TextColumn::make('price')->label('Selling Price')->alignEnd(),

                Tables\Columns\TextColumn::make('discount_price')->alignEnd()
                    ->description(fn(OrderProduct $orderProduct)=>$orderProduct->discount.' %'),

                Tables\Columns\TextColumn::make('quantity')->alignCenter(),

                Tables\Columns\TextColumn::make('profit/Loss')
                    ->badge()->alignEnd()
                    ->color(fn($state) => $state > 0 ? 'success' : 'danger')
                    ->getStateUsing(fn($record)=> ($record->discount != 0 ? $record->original_discount_price : $record->price ) - $record->original_buy_price)
                    ->formatStateUsing(fn($state) => $state ),

                Tables\Columns\TextColumn::make('sub_price')->prefix(' qty')->alignEnd(),

                   Tables\Columns\TextColumn::make('sub_price')->alignEnd()
                       ->summarize(Tables\Columns\Summarizers\Sum::make('sub_price')->money('mmk')->label('Total Amount'))

            ])
            ->defaultSort('id','desc')
            ->filters([
//                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
//                    Tables\Actions\ForceDeleteBulkAction::make(),
//                    Tables\Actions\RestoreBulkAction::make(),
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
            ->withoutGlobalScopes([
//                SoftDeletingScope::class,
            ]);
    }
}
