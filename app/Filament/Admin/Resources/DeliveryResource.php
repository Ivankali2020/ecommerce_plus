<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\DeliveryResource\Pages;
use App\Filament\Admin\Resources\DeliveryResource\RelationManagers;
use App\Models\Delivery;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Hamcrest\Core\Set;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DeliveryResource extends Resource
{
    protected static ?string $model = Delivery::class;

//    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Delivery Section';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\Group::make()->schema([

                    Forms\Components\Section::make()->schema([
                        Forms\Components\Select::make('seller_id')
                            ->required()->searchable()->preload()
                            ->hidden(Filament::getAuthGuard() == 'seller')
                            ->relationship('seller','name'),

                        Forms\Components\Select::make('region_id')->live()
                            ->required()->searchable()->preload()
                            ->afterStateUpdated(fn(Forms\Set $set)=>  $set('township_id',null))
                            ->relationship('region','mm_name'),

                        Forms\Components\Select::make('township_id')
                            ->hidden(fn(Forms\Get $get)=> blank($get('region_id')))
                            ->required()->searchable()->preload()
                            ->relationship('township','mm_name',modifyQueryUsing: fn($query,Forms\Get $get)=> $query->where('region_id',$get('region_id'))),

                        Forms\Components\TextInput::make('fees')
                            ->required()
                            ->numeric()->helperText('for only customer MMK')
                            ->default(0.00),


                    ])
                ]),

                Forms\Components\Group::make()->schema([

                    Forms\Components\Section::make('Shipping Infomation')->schema([
                        Forms\Components\Toggle::make('is_cod')->label('Cash On Delivery')
                            ->offIcon('heroicon-m-squares-2x2')
                            ->onIcon('heroicon-m-bolt')
                            ->helperText('Cash on delivery စနစ်')
                            ->required(),

                        Forms\Components\Toggle::make('is_pick_up')
                            ->label('Self Pick Up')
                            ->offIcon('heroicon-m-squares-2x2')
                            ->onIcon('heroicon-m-shield-exclamation')
                            ->helperText('ကိုယ်တိုင်လာယူစနစ်')
                            ->required(),

                        Forms\Components\TextInput::make('remark')
                            ->placeholder('၃ ကီလိုထက်ကျော်ရင် ထပ်မံ ကောက်ခံပါမည်')
                            ->maxLength(255),
                    ])

                ])


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('seller.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('region.mm_name')
                    ->description(fn(Delivery $delivery)=> $delivery->region->en_name)
                    ->sortable(),
                Tables\Columns\TextColumn::make('township.mm_name')
                    ->description(fn(Delivery $delivery)=> $delivery->region->en_name)
                    ->sortable(),
                Tables\Columns\TextColumn::make('fees')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_cod')->label('COD')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_pick_up')->label('SPU')
                    ->boolean(),
                Tables\Columns\TextColumn::make('remark')
                    ->searchable(),
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
            'index' => Pages\ListDeliveries::route('/'),
            'create' => Pages\CreateDelivery::route('/create'),
            'edit' => Pages\EditDelivery::route('/{record}/edit'),
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
