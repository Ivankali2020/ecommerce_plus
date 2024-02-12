<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ProductResource\Pages;
use App\Filament\Admin\Resources\ProductResource\RelationManagers;
use App\Helper\CheckFilamentSellerUser;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Brand;
use App\Models\Currency;
use App\Models\Exchange;
use App\Models\Product;
use App\Models\Variant;
use CodeWithDennis\FilamentSelectTree\SelectTree;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Form;
use Filament\Infolists\Components\Grid;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ViewRecord;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use function Symfony\Component\Translation\t;

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
                           ->searchable()->preload()->live(onBlur: true,debounce: 200)
                           ->hidden(Filament::getAuthGuard() == 'seller')
                           ->afterStateUpdated(fn(Forms\Set $set)=> $set('exchange_id',null))
                           ->required()->relationship('seller','name'),

                       Forms\Components\Select::make('exchange_id')
                           ->searchable()->preload()->label('Currency')
                           ->live(onBlur: true,debounce: 200)
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
                           ->searchable()->preload()->live(onBlur: true,debounce: 200)
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
                           ->live(onBlur: true,debounce: 200)
                           ->required()->placeholder('Product Name')
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
                           ->live(onBlur: true,debounce: 200)->label('Variants')->disabled(fn(Page $livewire) => $livewire instanceof EditRecord)
                           ->helperText('You have many product variants')
                           ->required(),

                       Forms\Components\Toggle::make('is_public')
                           ->onIcon('heroicon-m-bolt')
                           ->offIcon('heroicon-m-stop')
                           ->helperText('If you can publish when you want to show customers')
                           ->label('Published')->default(true)
                           ->required(),

                       Forms\Components\Toggle::make('is_recommended')
                           ->onIcon('heroicon-m-hand-thumb-up')
                           ->offIcon('heroicon-m-hand-thumb-down')
                           ->helperText('If you to recommend for your valuable customers')
                           ->label('Recommend')
                           ->required(),

                       Forms\Components\Section::make('Shipping')
                           ->description('If you want specific shipping data you can change below options')
                            ->schema([
                                Forms\Components\Toggle::make('is_return')
                                    ->onIcon('heroicon-m-shield-check')
                                    ->offIcon('heroicon-m-stop')
                                    ->label('Return Product')->default(true)
                                    ->helperText('This product can be returned')
                                    ->required(),

                                Forms\Components\Toggle::make('is_shipped')
                                    ->onIcon('heroicon-m-truck')
                                    ->offIcon('heroicon-m-stop')
                                    ->label('Shipping Product')->default(true)
                                    ->helperText('This product will be shipped')
                                    ->required(),
                            ])->compact()

                   ])->columns(2),

                   Forms\Components\Group::make()->schema([

                       Forms\Components\Section::make('Single Product Section')
                           ->description('If your product have a single item you should use this filed')
                           ->compact()
                           ->schema([

                               Forms\Components\TextInput::make('buy_price')
                                   ->placeholder('Original price')
                                   ->helperText('Calculate for profit and loss')->required(),

                               Forms\Components\TextInput::make('price')
                                   ->placeholder('Resell price')
                                   ->numeric()
                                   ->live(onBlur: true,debounce: 200)
                                   ->afterStateUpdated(function(Forms\Get $get,Forms\Set $set){
                                       //this is auto change discount price
                                       self::getDiscountPrice($get,$set);
                                   })
                                   ->required(fn(Forms\Get $get)=> !$get('has_variant'))
                                   ->prefix(fn(Forms\Get $get) => blank($get('exchange_id')) ? '' : Exchange::where('id',$get('exchange_id'))->with('currency')->first()->currency->symbol_native),


                               Forms\Components\TextInput::make('quantity')
                                   ->placeholder('Your in stock amount')
                                   ->numeric()->required(fn(Forms\Get $get)=> !$get('has_variant')),


                               Forms\Components\TextInput::make('discount')
                                   ->numeric()->suffix('%')->minValue(0)
                                   ->integer()->rules('max:100')
                                   ->live(onBlur: true,debounce: 200)
                                   ->afterStateUpdated(function(Forms\Get $get,Forms\Set $set){
                                       //this is auto change discount price
                                       self::getDiscountPrice($get,$set);
                                   })
                                   ->default(0),

                               Forms\Components\TextInput::make('discount_price')
                                   ->numeric()->label('Discount Price')
                                   ->prefix(fn(Forms\Get $get) => blank($get('exchange_id')) ? '' : Exchange::where('id',$get('exchange_id'))->with('currency')->first()->currency->symbol_native)
                                   ->default(0.0000),

                               Forms\Components\TextInput::make('MOQ')
                                   ->label('Min Amount Of Quantity(MOQ)')
                                   ->helperText('MOQ means  minimum amount of  of order quantity for wholesale')
                                   ->default(1)->numeric()
                                   ->required(fn(Forms\Get $get)=> !$get('has_variant')),

                                  Forms\Components\Toggle::make('is_whole_sale')
                                      ->onIcon('heroicon-m-hand-thumb-up')
                                      ->offIcon('heroicon-m-hand-thumb-down')
                                      ->helperText('For your product`s wholesale')
                                      ->label('WholeSales')
                                      ->required(),

                               Forms\Components\Repeater::make('attributes')
                                   ->reorderableWithButtons()
                                   ->schema([

                                       Forms\Components\Group::make()->schema([
                                           Forms\Components\Select::make('attribute_id')->live(onBlur: true,debounce: 200)
                                               ->required()->searchable()->preload()->label('Attribute')->placeholder('Select')
                                               ->afterStateUpdated(fn(Forms\Set $set)=>  $set('attribute_value_id',null))
                                               ->options(function (Forms\Get $get){
                                                   if (Filament::getAuthGuard() == 'seller'){
                                                       return Attribute::where('seller_id',CheckFilamentSellerUser::getSellerId())->pluck('name','id');
                                                   }
                                                   return Attribute::where('seller_id',$get('../../seller_id'))->pluck('name','id');
                                               }),

                                           Forms\Components\Select::make('attribute_value_id')
                                               ->multiple()
                                               ->hidden(fn(Forms\Get $get)=> blank($get('attribute_id')))
                                               ->required()->searchable()->preload()->label('Value')->placeholder('Select')
                                               ->options(fn(Forms\Get $get) => AttributeValue::where('attribute_id',$get('attribute_id'))->pluck('value','id')),

                                       ])->columns(2),

                                   ])->columnSpanFull(),

                               Forms\Components\Section::make('မြန်မာ ကျပ် ပြပုံစံ')
                                   ->compact()
                                   ->schema([
                                       Forms\Components\TextInput::make('mmk_price')
                                           ->label('မူရင်းစျေး')
                                           ->readOnly(),

                                       Forms\Components\TextInput::make('mmk_discount_price')
                                           ->readOnly()->label('လျော့စျေး'),

                               ])->columns(2)->visible(function(Forms\Get $get){
                                   if (!blank($get('exchange_id'))){
                                       $exchange = Exchange::where('id', $get('exchange_id'))->with('currency:id,code')->first();
                                       if ($exchange->currency->code != 'MMK'){
                                            return  true;
                                       }
                                   }
                                   return false;
                               }),

                               Forms\Components\SpatieMediaLibraryFileUpload::make('images')
                                   ->collection('images')->imageEditor()->multiple()
                                   ->image()->rules('required')->columnSpanFull(),

                           ])->columns(2)

                   ])->hidden(fn (Forms\Get $get) => $get('has_variant') || blank($get('exchange_id'))),

                   Forms\Components\Group::make()->schema([

                       Forms\Components\Repeater::make('variants')
                           ->relationship('variants')
                           ->collapsible()->reorderable()->cloneable()
                           ->schema([

                               Forms\Components\Grid::make()->schema([
                                   Forms\Components\Group::make()->schema([

                                       Forms\Components\TextInput::make('name')
                                           ->required()->placeholder('Product Name')
                                           ->maxLength(255),

                                       Forms\Components\TextInput::make('sku')
                                           ->unique(ignoreRecord: true)
                                           ->required(),

                                       Forms\Components\TextInput::make('buy_price')

                                           ->helperText('Calculate for profit and loss')->required(),

                                       Forms\Components\TextInput::make('price')
                                           ->placeholder('Resell price')
                                           ->numeric()->live(onBlur: true,debounce: 200)->required()
                                           ->afterStateUpdated(function(Forms\Get $get,Forms\Set $set){
                                               //this is auto change discount price
                                               self::getDiscountPrice($get,$set,true);
                                           })
                                           ->prefix(fn(Forms\Get $get) => blank($get('../../exchange_id')) ? '' : Exchange::where('id',$get('../../exchange_id'))->with('currency')->first()->currency->symbol_native),

                                       Forms\Components\Group::make()->schema([
                                           Forms\Components\TextInput::make('discount')
                                               ->numeric()->prefix('%')->integer()->maxValue(100)
                                               ->live(onBlur: true,debounce: 200)
                                               ->afterStateUpdated(function(Forms\Get $get,Forms\Set $set){
                                                   //this is auto change discount price
                                                   self::getDiscountPrice($get,$set,true);
                                               })
                                               ->default(0),

                                           Forms\Components\TextInput::make('discount_price')
                                               ->numeric()->label('Discount Price')
                                               ->prefix(fn(Forms\Get $get) => blank($get('exchange_id')) ? '' : Exchange::where('id',$get('exchange_id'))->with('currency')->first()->currency->symbol_native)
                                               ->default(0.0000),

                                       ])->columns(2),

                                       Forms\Components\TextInput::make('quantity')
                                           ->placeholder('Your in stock amount')
                                           ->numeric()->required(),

                                       Forms\Components\TextInput::make('MOQ')
                                           ->label('Min Amount Of Quantity(MOQ)')
                                           ->helperText('MOQ means minimum amount of order quantity')
                                           ->default(1)
                                           ->numeric(),

                                   ]),

                                   Forms\Components\Group::make()->schema([

                                       //this is myanmar section
                                       Forms\Components\Section::make()
                                           ->description('မြန်မာ ကျပ် ပြပုံစံ')
                                           ->compact()
                                           ->schema([
                                               Forms\Components\TextInput::make('mmk_price')
                                                   ->label('မူရင်းစျေး')
                                                   ->readOnly()->columnSpan(fn(Page $livewire) => $livewire instanceof ViewRecord ? 'full' : ''),

                                               Forms\Components\TextInput::make('mmk_discount_price')
                                                   ->readOnly()->label('လျော့စျေး')->columnSpan(fn(Page $livewire) => $livewire instanceof ViewRecord ? dd($livewire) : '1'),

                                           ])->columns(2)->visible(function(Forms\Get $get){
                                               if (!blank($get('../../exchange_id'))){
                                                   $exchange = Exchange::where('id', $get('../../exchange_id'))->with('currency:id,code')->first();
                                                   if ($exchange->currency->code != 'MMK'){
                                                       return  true;
                                                   }
                                               }
                                               return false;
                                           }),

                                       Forms\Components\Repeater::make('attributes')
                                           ->reorderableWithButtons()
                                           ->schema([

                                               Forms\Components\Group::make()->schema([

                                                   Forms\Components\Select::make('attribute_id')->live(onBlur: true,debounce: 200)
                                                       ->required()->searchable()->preload()->label('Attribute')->placeholder('Select')
                                                       ->afterStateUpdated(fn(Forms\Set $set)=>  $set('attribute_value_id',null))
                                                       ->options(function (Forms\Get $get){
                                                           // ဒါက admin အတွက်စစ်တာပါ ဗျာ
                                                           if (Filament::getAuthGuard() == 'seller'){
                                                               return Attribute::where('seller_id',CheckFilamentSellerUser::getSellerId())->pluck('name','id');
                                                           }
                                                           return Attribute::where('seller_id',$get('../../../../seller_id'))->pluck('name','id');
                                                       }),

                                                   Forms\Components\Select::make('attribute_value_id')
                                                       ->multiple()
                                                       ->hidden(fn(Forms\Get $get)=> blank($get('attribute_id')))
                                                       ->required()->searchable()->preload()->label('Value')->placeholder('Select')
                                                       ->options(fn(Forms\Get $get) => AttributeValue::where('attribute_id',$get('attribute_id'))->pluck('value','id')),


                                               ])->columns(2),


                                               Forms\Components\Section::make('မြန်မာ ကျပ် ပြပုံစံ')
                                                   ->compact()
                                                   ->schema([
                                                       Forms\Components\TextInput::make('mmk_price')
                                                           ->label('မူရင်းစျေး')->numeric()->readOnly(),

                                                       Forms\Components\TextInput::make('mmk_discount_price')
                                                           ->readOnly()->label('လျော့စျေး')->numeric(),

                                                   ])->columns(2)->visible(function(Forms\Get $get){
                                                       if (!blank($get('exchange_id'))){
                                                           $exchange = Exchange::where('id', $get('exchange_id'))->with('currency:id,code')->first();
                                                           if ($exchange->currency->code != 'MMK'){
                                                               return  true;
                                                           }
                                                       }
                                                       return false;
                                                   }),


                                           ])->columnSpanFull()->hidden(function(Forms\Get $get){
                                               // ဒါက admin အတွက်စစ်တာပါ ဗျာ
                                               if (Filament::getAuthGuard() == 'admin'){
                                                   return blank($get('../../seller_id'));
                                               }
                                               return false;
                                           }),
                                   ])->columnSpan(1),

                               ])->columns(2),

                               Forms\Components\SpatieMediaLibraryFileUpload::make('images')
                                   ->collection('images')->imageEditor()->multiple()
                                   ->helperText('Clone Action လုပ်ပါက images ကို နောက်ဆုံးမှ ထည့်ပေးပါရန် (သို့) edit မှာ clone ပေးပါရန်')
                                   ->rules('required')->columnSpanFull(),

                           ])
                           ->columns(3)
                           ->visible(fn (Forms\Get $get) => $get('has_variant') && !blank($get('exchange_id')))
                   ])

               ])

            ]);
    }

    public static function getDiscountPrice($get,$set,$forVariants = false)
    {

        if($get('discount') != null  &&  !blank($get('price'))){
            $discountCalculate =  $get('price') - ( $get('discount') / 100 * $get('price') );
            $set('discount_price',(float)$discountCalculate);
        }

        if( $get('discount') == 0){
            $set('discount_price',0.00);
         }else{
            if (!blank($get('price'))){
                self::getDiscountMmk($get,$set,$forVariants);
            }
        }
    }

    /**
     * @param Forms\Get $get
     * @param Forms\Set $set
     * @return void
     */
    public static function getDiscountMmk($get,$set,$forVariants): void
    {

        if ($forVariants){
            $exchangeID = $get('../../exchange_id');
        }else{
            $exchangeID = $get('exchange_id');
        }

        if ($exchangeID){
            $exchange = Exchange::where('id', $exchangeID)->with('currency:id,code')->first();
            if ($exchange->currency->code != 'MMK'){
                $getMMKExchange = Exchange::whereHas('currency',function ($q){
                    return $q->where('code','MMK');
                })->first();

                $getMMKPriceFromSelectedCurrency = ($getMMKExchange->rate / $exchange->rate) * $get('price');
                $removeLastTowDigitsToZero = self::modifyPrice($getMMKPriceFromSelectedCurrency);
                $set('mmk_price', $removeLastTowDigitsToZero);

                //calculate discount price mmk_price based
                $discountCalculateForMMK = $getMMKPriceFromSelectedCurrency - ( $get('discount') / 100 * $getMMKPriceFromSelectedCurrency );
                $removeLastTowDigitsToZeroForMMKDiscountPrice = self::modifyPrice($discountCalculateForMMK);

                $set('mmk_discount_price',$removeLastTowDigitsToZeroForMMKDiscountPrice);

            }
        }

    }

    public static function modifyPrice($price)
    {
        // Round the price to the nearest hundred
        $roundedPrice = round($price, -2);

        // Ensure the last two digits are "00"
        $modifiedPrice = $roundedPrice - ($roundedPrice % 100);

        return $modifiedPrice;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('brand.name')
                    ->description(fn(Product $product)=> $product->category->name)
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
                Tables\Actions\ReplicateAction::make()
                    ->modalWidth('sm')
                    ->form([
                        Forms\Components\TextInput::make('name'),
                        Forms\Components\TextInput::make('sku'),
                        Forms\Components\SpatieMediaLibraryFileUpload::make('images')->collection('images')->previewable(false)->multiple()
                    ])
                    ->mountUsing(function (Forms\ComponentContainer $form,Product $record){
                        $form->fill([
                            'sku' => uniqid(),
                            'name' => $record->name,
                            'images' => $record->images
                        ]);
                    })->beforeReplicaSaved(function (Model $replica,array $data){
                        $replica->fill($data);
                    })->after(function (Model $replica,Product $product){
                        $medias = $product->getMedia('images')->first();
                         $medias->copy($replica,'images');
                    })
                    ->hidden(fn(Product $product)=>$product->has_variant),
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make()->modalWidth(fn(Product $product)=>$product->has_variant?'5xl':'4xl')->slideOver(),
            ])->defaultSort('id','desc')
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
            ->with('variants')
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
