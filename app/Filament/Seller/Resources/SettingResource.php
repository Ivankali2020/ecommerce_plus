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

    public static function getNavigationLabel(): string
    {
        return __('filament/resources/setting-resource.navigation.label');
    }

    public static function getModelLabel(): string
    {
        return __('filament/resources/setting-resource.navigation.single');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament/resources/setting-resource.navigation.plural');
    }

    public static function canCreate(): bool
    {
        return false; // Disable creation of settings through the Filament interface
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make(__('filament/resources/setting-resource.form.tabs.general'))
                    ->tabs([
                        Tabs\Tab::make(__('filament/resources/setting-resource.form.tabs.general'))
                            ->schema([
                                Forms\Components\TextInput::make('data.point_percentage')
                                    ->label(__('filament/resources/setting-resource.form.general.point_percentage.label'))
                                    ->helperText(__('filament/resources/setting-resource.form.general.point_percentage.helper_text'))
                                    ->numeric()
                                    ->required(),
                                Forms\Components\TextInput::make('data.tax_percentage')
                                    ->label(__('filament/resources/setting-resource.form.general.tax_percentage.label'))
                                    ->helperText(__('filament/resources/setting-resource.form.general.tax_percentage.helper_text'))
                                    ->numeric()
                                    ->required(),
                                Forms\Components\TextInput::make('data.referral_points')
                                    ->label(__('filament/resources/setting-resource.form.general.referral_points.label'))
                                    ->helperText(__('filament/resources/setting-resource.form.general.referral_points.helper_text'))
                                    ->numeric()
                                    ->required(),
                            ]),

                        Tabs\Tab::make(__('filament/resources/setting-resource.form.tabs.social_media'))
                            ->schema([
                                Forms\Components\TextInput::make('data.facebook')
                                    ->label(__('filament/resources/setting-resource.form.social_media.facebook.label'))
                                    ->placeholder(__('filament/resources/setting-resource.form.social_media.facebook.placeholder'))
                                    ->url()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('data.twitter')
                                    ->label(__('filament/resources/setting-resource.form.social_media.twitter.label'))
                                    ->placeholder(__('filament/resources/setting-resource.form.social_media.twitter.placeholder'))
                                    ->url()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('data.youtube')
                                    ->label(__('filament/resources/setting-resource.form.social_media.youtube.label'))
                                    ->placeholder(__('filament/resources/setting-resource.form.social_media.youtube.placeholder'))
                                    ->url()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('data.instagram')
                                    ->label(__('filament/resources/setting-resource.form.social_media.instagram.label'))
                                    ->placeholder(__('filament/resources/setting-resource.form.social_media.instagram.placeholder'))
                                    ->url()
                                    ->maxLength(255),
                            ]),

                        Tabs\Tab::make(__('filament/resources/setting-resource.form.tabs.content_pages'))
                            ->schema([
                                RichEditor::make('data.privacy_policy')
                                    ->label(__('filament/resources/setting-resource.form.content_pages.privacy_policy.label'))
                                    ->helperText(__('filament/resources/setting-resource.form.content_pages.privacy_policy.helper_text'))
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
                                    ->label(__('filament/resources/setting-resource.form.content_pages.terms_conditions.label'))
                                    ->helperText(__('filament/resources/setting-resource.form.content_pages.terms_conditions.helper_text'))
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
                                    ->label(__('filament/resources/setting-resource.form.content_pages.about_us.label'))
                                    ->helperText(__('filament/resources/setting-resource.form.content_pages.about_us.helper_text'))
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
                                    ->label(__('filament/resources/setting-resource.form.content_pages.returns_exchanges.label'))
                                    ->helperText(__('filament/resources/setting-resource.form.content_pages.returns_exchanges.helper_text'))
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

                        Tabs\Tab::make(__('filament/resources/setting-resource.form.tabs.faq'))
                            ->schema([
                                Repeater::make('data.faq')
                                    ->label(__('filament/resources/setting-resource.form.faq.label'))
                                    ->schema([
                                        Forms\Components\TextInput::make('question')
                                            ->label(__('filament/resources/setting-resource.form.faq.question.label'))
                                            ->placeholder(__('filament/resources/setting-resource.form.faq.question.placeholder'))
                                            ->required()
                                            ->maxLength(255),
                                        RichEditor::make('answer')
                                            ->label(__('filament/resources/setting-resource.form.faq.answer.label'))
                                            ->placeholder(__('filament/resources/setting-resource.form.faq.answer.placeholder'))
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
                                    ->addActionLabel(__('filament/resources/setting-resource.form.faq.add_item'))
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
                    ->label(__('filament/resources/setting-resource.table.columns.point_percentage'))
                    ->suffix(' % ')
                    ->sortable(),

                Tables\Columns\TextColumn::make('data.referral_points')
                    ->label(__('filament/resources/setting-resource.table.columns.referral_points'))
                    ->suffix(' PT ')
                    ->sortable(),

                Tables\Columns\TextColumn::make('data.tax_percentage')
                    ->label(__('filament/resources/setting-resource.table.columns.tax_percentage'))
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
