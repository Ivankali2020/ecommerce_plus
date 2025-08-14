<?php

namespace App\Filament\Seller\Resources;

use App\Filament\Seller\Resources\SettingResource\Pages;
use App\Filament\Seller\Resources\SettingResource\RelationManagers;
use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    public static function canCreate(): bool
    {
        return false; // Disable creation of settings through the Filament interface
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Repeater::make('data')
                    ->label('Settings')
                    ->schema([

                        Forms\Components\TextInput::make('key')
                            ->label('Key')
                            ->required()
                            ->disabled()
                            ->readOnly()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('value')
                            ->label('Value')
                            ->required()
                            ->numeric()
                            ->maxLength(255),
                    ])
                    ->columns(2)
                    ->deletable(false)
                    ->addable(false)
                    ->defaultItems(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                
                Tables\Columns\TextColumn::make('data.point_percentage')
                    ->label('Point Percentage')
                    ->getStateUsing(function ($record) {
                        $data = $record->data;
                        return $data[0]['value'] ?? '-';
                    })
                    ->suffix(' % ')
                    ->sortable()
                    ,

                Tables\Columns\TextColumn::make('data.referral_points')
                    ->label('Referral Points')
                    ->getStateUsing(function ($record) {
                        $data = is_array($record->data) ? $record->data : json_decode($record->data, true);
                       return $data[2]['value'] ?? '-';
                    })
                    ->suffix(' PT ')
                    ->sortable()
                    ,

                
                Tables\Columns\TextColumn::make('data.tax_percentage')
                    ->label('Tax Percentage')
                    ->suffix(' % ')
                    ->getStateUsing(function ($record) {
                        $data = is_array($record->data) ? $record->data : json_decode($record->data, true);
                        return $data[1]['value'] ?? '-';
                    })
                    ->sortable()
                    ,

            ])
            ->paginated(false)
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListSettings::route('/'),
            'create' => Pages\CreateSetting::route('/create'),
            'edit' => Pages\EditSetting::route('/{record}/edit'),
        ];
    }
}
