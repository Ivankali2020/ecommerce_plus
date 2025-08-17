<?php

namespace App\Filament\Admin\Resources\Categories\RelationManagers;

use App\Models\Category;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class CategoriesRelationManager extends RelationManager
{
    protected static string $relationship = 'subCategories';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([

                // this is for seller
                Hidden::make('seller_id')
                    ->default(function () {
                        if (Filament::getAuthGuard() == 'seller') {
                            return Filament::auth()->id();
                        }

                        return null;
                    }),
                // end

                Select::make('seller_id')
                    ->searchable()->preload()
                    // this is for seller
                    ->disabled(Filament::getAuthGuard() == 'seller')
                    ->hidden(Filament::getAuthGuard() == 'seller')
                    // end
                    ->required()->relationship('seller', 'name'),

                TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                Textarea::make('description')
                    ->maxLength(16777215),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('seller.name')
                    ->hidden(Filament::getAuthGuard() == 'seller'),
                TextColumn::make('name')
                    ->description(fn (Category $category) => Str::limit($category->description, 80)),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
                ForceDeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
