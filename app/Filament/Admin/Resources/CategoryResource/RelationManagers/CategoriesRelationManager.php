<?php

namespace App\Filament\Admin\Resources\CategoryResource\RelationManagers;

use App\Models\Category;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class CategoriesRelationManager extends RelationManager
{
    protected static string $relationship = 'subCategories';

    public function form(Form $form): Form
    {
        return $form
            ->schema([

                // this is for seller
                Forms\Components\Hidden::make('seller_id')
                    ->default(function () {
                        if (Filament::getAuthGuard() == 'seller') {
                            return Filament::auth()->id();
                        }

                        return null;
                    }),
                // end

                Forms\Components\Select::make('seller_id')
                    ->searchable()->preload()
                    // this is for seller
                    ->disabled(Filament::getAuthGuard() == 'seller')
                    ->hidden(Filament::getAuthGuard() == 'seller')
                    // end
                    ->required()->relationship('seller', 'name'),

                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Textarea::make('description')
                    ->maxLength(16777215),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('seller.name')
                    ->hidden(Filament::getAuthGuard() == 'seller'),
                Tables\Columns\TextColumn::make('name')
                    ->description(fn (Category $category) => Str::limit($category->description, 80)),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
