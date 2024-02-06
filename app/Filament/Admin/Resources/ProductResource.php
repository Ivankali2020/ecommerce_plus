<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ProductResource\Pages;
use App\Filament\Admin\Resources\ProductResource\RelationManagers;
use App\Helper\CheckFilamentSellerUser;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Brand;
use App\Models\Exchange;
use App\Models\Product;
use CodeWithDennis\FilamentSelectTree\SelectTree;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\Grid;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

//    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Business Section';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
               Forms\Components\Grid::make()->schema([
                   Forms\Components\Group::make()->schema([

                       Forms\Components\Select::make('seller_id')
                           ->searchable()->preload()->live()
                           ->hidden(Filament::getAuthGuard() == 'seller')
                           ->afterStateUpdated(fn(Forms\Set $set)=> $set('exchange_id',null))
                           ->required()->relationship('seller','name'),

                       Forms\Components\Select::make('exchange_id')
                           ->searchable()->preload()->label('Currency')
                           ->live()
                           ->hidden(fn(Forms\Get $get) => blank($get('seller_id')) &&  Filament::getAuthGuard() == 'admin' )
                           ->options(function(Forms\Get $get){
                               $seller_id = $get('seller_id') ?? CheckFilamentSellerUser::getSellerId();
                               $data = Exchange::where('seller_id',$seller_id)->with('currency')->get()->map(function ($item){
                                            return [$item->id => $item->currency->code];
                                        });
                               $currencies = [];
                               foreach ($data as $da){
                                   $currencies += $da;
                               }
                               return $currencies;
                            })
                           ->required(),

                       Forms\Components\Select::make('brand_id')
                           ->searchable()->preload()->live()
                           ->required()->options(function (Forms\Get $get){
                               $seller_id = $get('seller_id') ?? CheckFilamentSellerUser::getSellerId();
                               return Brand::where('seller_id',$seller_id)->pluck('name','id');
                           }),

                       SelectTree::make('category_id')
                           ->searchable()->label('Category')->searchable()
                           ->required()->enableBranchNode()->placeholder('Please select a category')
                           ->relationship('category','name','parent_id',modifyQueryUsing: function (Forms\Get $get,$query){
                               $seller_id = $get('seller_id') ?? CheckFilamentSellerUser::getSellerId();
                               return $query->where('seller_id',$seller_id);
                           }),

                       Forms\Components\TextInput::make('name')
                           ->required()
                           ->maxLength(255),

                       Forms\Components\TextInput::make('sku')
                           ->default(Str::upper(uniqid()))
                           ->required(fn(Forms\Get $get)=> !$get('has_variant'))
                           ->hidden(fn(Forms\Get $get)=> $get('has_variant'))
                           ->maxLength(255),

                       Forms\Components\RichEditor::make('description')
                           ->required()
                           ->maxLength(16777215)
                           ->columnSpanFull(),

                       Forms\Components\Toggle::make('has_variant')
                           ->onIcon('heroicon-m-squares-2x2')
                           ->offIcon('heroicon-m-stop')
                           ->live()->label('Variants')
                           ->helperText('You can change variation section')
                           ->required(),

                       Forms\Components\Toggle::make('is_public')
                           ->onIcon('heroicon-m-bolt')
                           ->offIcon('heroicon-m-stop')
                           ->label('Published')
                           ->required(),

                       Forms\Components\Toggle::make('is_recommended')
                           ->onIcon('heroicon-m-hand-thumb-up')
                           ->offIcon('heroicon-m-hand-thumb-down')
                           ->label('Recommend')
                           ->required(),

                       Forms\Components\Section::make('Shipping')
                            ->schema([

                                Forms\Components\Toggle::make('is_return')
                                    ->onIcon('heroicon-m-shield-check')
                                    ->offIcon('heroicon-m-stop')
                                    ->label('Return Product')
                                    ->helperText('This product can be returned')
                                    ->required(),

                                Forms\Components\Toggle::make('is_shipped')
                                    ->onIcon('heroicon-m-truck')
                                    ->offIcon('heroicon-m-stop')
                                    ->label('Shipping Product')
                                    ->helperText('This product will be shipped')
                                    ->required(),
                            ])

                   ])->columns(2),

                   Forms\Components\Group::make()->schema([

                       Forms\Components\Section::make('Single Product Section')
                           ->description('If your product have a single item you should use this filed')
                           ->schema([

                               Forms\Components\TextInput::make('price')
                                   ->numeric()
                                   ->afterStateUpdated(function(Forms\Get $get,Forms\Set $set){
                                       //this is auto change discount price
                                       self::getDiscountPrice($get,$set);
                                   })
                                   ->required(fn(Forms\Get $get)=> !$get('has_variant'))
                                   ->prefix(fn(Forms\Get $get) => blank($get('exchange_id')) ? '' : Exchange::where('id',$get('exchange_id'))->with('currency')->first()->currency->symbol_native),

                               Forms\Components\TextInput::make('quantity')
                                   ->required(fn(Forms\Get $get)=> !$get('has_variant'))
                                   ->numeric(),

                               Forms\Components\TextInput::make('discount')
                                   ->numeric()->suffix('%')->minValue(0)->integer()->maxValue(100)
                                   ->live()
                                   ->afterStateUpdated(function(Forms\Get $get,Forms\Set $set){
                                       //this is auto change discount price
                                       self::getDiscountPrice($get,$set);
                                   })
                                   ->default(0),

                               Forms\Components\TextInput::make('discount_price')
                                   ->numeric()->label('Discount Price')
                                   ->prefix(fn(Forms\Get $get) => blank($get('exchange_id')) ? '' : Exchange::where('id',$get('exchange_id'))->with('currency')->first()->currency->symbol_native)
                                   ->default(0.0000),

                               Forms\Components\Repeater::make('attributes')
                                   ->reorderableWithButtons()
                                   ->schema([

                                       Forms\Components\Section::make()->schema([
                                           Forms\Components\Select::make('attribute_id')->live()
                                               ->required()->searchable()->preload()
                                               ->afterStateUpdated(fn(Forms\Set $set)=>  $set('attribute_value_id',null))
                                               ->options(function (Forms\Get $get){
                                                   if (Filament::getAuthGuard() == 'seller'){
                                                       return Attribute::where('seller_id',CheckFilamentSellerUser::getSellerId())->pluck('name','id');
                                                   }
                                                   return Attribute::where('seller_id',$get('../../seller_id'))->pluck('name','id');
                                               }),

                                           Forms\Components\Select::make('attribute_value_id')
                                               ->hidden(fn(Forms\Get $get)=> blank($get('attribute_id')))
                                               ->required()->searchable()->preload()
                                               ->options(fn(Forms\Get $get) => AttributeValue::where('attribute_id',$get('attribute_id'))->pluck('value','id')),

                                       ])->columns(2),

                                   ])->columnSpanFull(),

                               Forms\Components\SpatieMediaLibraryFileUpload::make('images')
                                   ->collection('images')->imageEditor()->multiple()
                                   ->image()->rules('required')->columnSpanFull(),

                           ])->columns(2)

                   ])->hidden(fn (Forms\Get $get) => $get('has_variant')),

                   Forms\Components\Group::make()->schema([

                       Forms\Components\Repeater::make('variants')
                           ->relationship('variants')
                           ->collapsible()->reorderableWithButtons()->reorderable()
                           ->schema([

                               Forms\Components\TextInput::make('sku')->required()->default(uniqid()),

                               Forms\Components\TextInput::make('price')
                                   ->numeric()->live()
                                   ->afterStateUpdated(function(Forms\Get $get,Forms\Set $set){
                                       //this is auto change discount price
                                       self::getDiscountPrice($get,$set);
                                   })
                                   ->prefix(fn(Forms\Get $get) => blank($get('../../exchange_id')) ? '' : Exchange::where('id',$get('../../exchange_id'))->with('currency')->first()->currency->symbol_native),

                               Forms\Components\TextInput::make('quantity')
                                   ->numeric(),

                               Forms\Components\TextInput::make('discount')
                                   ->numeric()->prefix('%')->integer()->maxValue(100)
                                   ->live()
                                   ->afterStateUpdated(function(Forms\Get $get,Forms\Set $set){
                                       //this is auto change discount price
                                       self::getDiscountPrice($get,$set);
                                   })
                                   ->default(0),

                               Forms\Components\TextInput::make('discount_price')
                                   ->numeric()->label('Discount Price')
                                   ->prefix(fn(Forms\Get $get) => blank($get('exchange_id')) ? '' : Exchange::where('id',$get('exchange_id'))->with('currency')->first()->currency->symbol_native)
                                   ->default(0.0000),

                               Forms\Components\Repeater::make('attributes')
                                   ->reorderableWithButtons()
                                   ->schema([

                                       Forms\Components\Section::make()->schema([

                                       Forms\Components\Select::make('attribute_id')->live()
                                           ->required()->searchable()->preload()
                                           ->afterStateUpdated(fn(Forms\Set $set)=>  $set('attribute_value_id',null))
                                           ->options(function (Forms\Get $get){
                                               // ဒါက admin အတွက်စစ်တာပါ ဗျာ
                                               if (Filament::getAuthGuard() == 'seller'){
                                                   return Attribute::where('seller_id',CheckFilamentSellerUser::getSellerId())->pluck('name','id');
                                               }
                                               return Attribute::where('seller_id',$get('../../../../seller_id'))->pluck('name','id');
                                           }),

                                       Forms\Components\Select::make('attribute_value_id')
                                           ->hidden(fn(Forms\Get $get)=> blank($get('attribute_id')))
                                           ->required()->searchable()->preload()
                                           ->options(fn(Forms\Get $get) => AttributeValue::where('attribute_id',$get('attribute_id'))->pluck('value','id')),


                                       ])->columns(2),

                                   ])->columnSpanFull()->hidden(function(Forms\Get $get){
                                       // ဒါက admin အတွက်စစ်တာပါ ဗျာ
                                       if (Filament::getAuthGuard() == 'admin'){
                                           return blank($get('../../seller_id'));
                                       }
                                       return false;
                                   }),

                               Forms\Components\SpatieMediaLibraryFileUpload::make('images')
                                   ->collection('images')->imageEditor()->multiple()
                                   ->image()->rules('required')->columnSpanFull(),

                           ])
                           ->columns(3)
                           ->visible(fn (Forms\Get $get) => $get('has_variant'))
                   ])

               ])

            ]);
    }

    public static function getDiscountPrice($get,$set)
    {

        if($get('discount') != null && $get('discount') != 0 &&  !blank($get('price'))){
            $discountCalculate =  $get('price') - ( $get('discount') / 100 * $get('price') );
            $set('discount_price',$discountCalculate);
        }

    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('brand.name')
//                    ->description(fn(Product $product)=> $product->category->name)
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->description(fn(Product $product)=> $product->sku)
                    ->searchable(),

                Tables\Columns\TextColumn::make('exchange.currency.code')
                    ->description(fn(Product $product)=> $product->exchange->currency->name)
                    ->sortable(),

                Tables\Columns\IconColumn::make('has_variant')
                    ->boolean(),

                Tables\Columns\IconColumn::make('is_public')
                    ->boolean(),

                Tables\Columns\IconColumn::make('is_recommended')
                    ->boolean(),

                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

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
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make()->slideOver(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
