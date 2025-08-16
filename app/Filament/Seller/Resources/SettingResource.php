<?php

namespace App\Filament\Seller\Resources;

use App\Filament\Seller\Resources\SettingResource\Pages;
use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

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
                Tabs::make('Settings')
                    ->tabs([
                        Tabs\Tab::make('General Settings')
                            ->schema([
                                Forms\Components\TextInput::make('data.point_percentage')
                                    ->label('Point Percentage')
                                    ->numeric()
                                    ->required(),
                                Forms\Components\TextInput::make('data.tax_percentage')
                                    ->label('Tax Percentage')
                                    ->numeric()
                                    ->required(),
                                Forms\Components\TextInput::make('data.referral_points')
                                    ->label('Referral Points')
                                    ->numeric()
                                    ->required(),
                            ]),

                        Tabs\Tab::make('Social Media')
                            ->schema([
                                Forms\Components\TextInput::make('data.facebook')
                                    ->label('Facebook URL')
                                    ->url()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('data.twitter')
                                    ->label('Twitter URL')
                                    ->url()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('data.youtube')
                                    ->label('YouTube URL')
                                    ->url()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('data.instagram')
                                    ->label('Instagram URL')
                                    ->url()
                                    ->maxLength(255),
                            ]),

                        Tabs\Tab::make('Content Pages')
                            ->schema([
                                RichEditor::make('data.privacy_policy')
                                    ->label('Privacy Policy')
                                    ->toolbarButtons([
                                        'bold',
                                        'italic',
                                        'underline',
                                        'link',
                                        'bulletList',
                                        'orderedList',
                                        'h2',
                                        'h3',
                                        'blockquote',
                                    ])
                                    ->columnSpanFull(),

                                RichEditor::make('data.terms_conditions')
                                    ->label('Terms & Conditions')
                                    ->toolbarButtons([
                                        'bold',
                                        'italic',
                                        'underline',
                                        'link',
                                        'bulletList',
                                        'orderedList',
                                        'h2',
                                        'h3',
                                        'blockquote',
                                    ])
                                    ->columnSpanFull(),

                                RichEditor::make('data.about_us')
                                    ->label('About Us')
                                    ->toolbarButtons([
                                        'bold',
                                        'italic',
                                        'underline',
                                        'link',
                                        'bulletList',
                                        'orderedList',
                                        'h2',
                                        'h3',
                                        'blockquote',
                                    ])
                                    ->columnSpanFull(),

                                RichEditor::make('data.returns_exchanges')
                                    ->label('Returns & Exchanges Policy')
                                    ->toolbarButtons([
                                        'bold',
                                        'italic',
                                        'underline',
                                        'link',
                                        'bulletList',
                                        'orderedList',
                                        'h2',
                                        'h3',
                                        'blockquote',
                                    ])
                                    ->columnSpanFull(),
                            ]),

                        Tabs\Tab::make('FAQ')
                            ->schema([
                                Repeater::make('data.faq')
                                    ->label('Frequently Asked Questions')
                                    ->schema([
                                        Forms\Components\TextInput::make('question')
                                            ->label('Question')
                                            ->required()
                                            ->maxLength(255),
                                        RichEditor::make('answer')
                                            ->label('Answer')
                                            ->required()
                                            ->toolbarButtons([
                                                'bold',
                                                'italic',
                                                'underline',
                                                'link',
                                                'bulletList',
                                                'orderedList',
                                            ])
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(2)
                                    ->collapsed()
                                    ->itemLabel(fn (array $state): ?string => $state['question'] ?? null)
                                    ->addActionLabel('Add FAQ Item')
                                    ->reorderable()
                                    ->collapsible()
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                Tables\Columns\TextColumn::make('data.point_percentage')
                    ->label('Point Percentage')
                    ->suffix(' % ')
                    ->sortable(),

                Tables\Columns\TextColumn::make('data.referral_points')
                    ->label('Referral Points')
                    ->suffix(' PT ')
                    ->sortable(),

                Tables\Columns\TextColumn::make('data.tax_percentage')
                    ->label('Tax Percentage')
                    ->suffix(' % ')
                    ->sortable(),

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
