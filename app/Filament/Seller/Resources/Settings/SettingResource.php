<?php

namespace App\Filament\Seller\Resources\Settings;

use App\Filament\Seller\Resources\Settings\Pages\CreateSetting;
use App\Filament\Seller\Resources\Settings\Pages\EditSetting;
use App\Filament\Seller\Resources\Settings\Pages\ListSettings;
use App\Models\Setting;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

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

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make(__('filament/resources/setting-resource.form.tabs.general'))
                    ->tabs([
                        Tab::make(__('filament/resources/setting-resource.form.tabs.general'))
                            ->schema([
                                TextInput::make('data.point_percentage')
                                    ->label(__('filament/resources/setting-resource.form.general.point_percentage.label'))
                                    ->helperText(__('filament/resources/setting-resource.form.general.point_percentage.helper_text'))
                                    ->numeric()
                                    ->required(),
                                TextInput::make('data.tax_percentage')
                                    ->label(__('filament/resources/setting-resource.form.general.tax_percentage.label'))
                                    ->helperText(__('filament/resources/setting-resource.form.general.tax_percentage.helper_text'))
                                    ->numeric()
                                    ->required(),
                                TextInput::make('data.referral_points')
                                    ->label(__('filament/resources/setting-resource.form.general.referral_points.label'))
                                    ->helperText(__('filament/resources/setting-resource.form.general.referral_points.helper_text'))
                                    ->numeric()
                                    ->required(),
                            ]),

                        Tab::make(__('filament/resources/setting-resource.form.tabs.social_media'))
                            ->schema([
                                TextInput::make('data.facebook')
                                    ->label(__('filament/resources/setting-resource.form.social_media.facebook.label'))
                                    ->placeholder(__('filament/resources/setting-resource.form.social_media.facebook.placeholder'))
                                    ->url()
                                    ->maxLength(255),
                                TextInput::make('data.twitter')
                                    ->label(__('filament/resources/setting-resource.form.social_media.twitter.label'))
                                    ->placeholder(__('filament/resources/setting-resource.form.social_media.twitter.placeholder'))
                                    ->url()
                                    ->maxLength(255),
                                TextInput::make('data.youtube')
                                    ->label(__('filament/resources/setting-resource.form.social_media.youtube.label'))
                                    ->placeholder(__('filament/resources/setting-resource.form.social_media.youtube.placeholder'))
                                    ->url()
                                    ->maxLength(255),
                                TextInput::make('data.instagram')
                                    ->label(__('filament/resources/setting-resource.form.social_media.instagram.label'))
                                    ->placeholder(__('filament/resources/setting-resource.form.social_media.instagram.placeholder'))
                                    ->url()
                                    ->maxLength(255),
                            ]),

                        Tab::make(__('filament/resources/setting-resource.form.tabs.content_pages'))
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

                        Tab::make(__('filament/resources/setting-resource.form.tabs.faq'))
                            ->schema([
                                Repeater::make('data.faq')
                                    ->label(__('filament/resources/setting-resource.form.faq.label'))
                                    ->schema([
                                        TextInput::make('question')
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

                TextColumn::make('data.point_percentage')
                    ->label(__('filament/resources/setting-resource.table.columns.point_percentage'))
                    ->suffix(' % ')
                    ->sortable(),

                TextColumn::make('data.referral_points')
                    ->label(__('filament/resources/setting-resource.table.columns.referral_points'))
                    ->suffix(' PT ')
                    ->sortable(),

                TextColumn::make('data.tax_percentage')
                    ->label(__('filament/resources/setting-resource.table.columns.tax_percentage'))
                    ->suffix(' % ')
                    ->sortable(),

            ])
            ->paginated(false)
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
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
            'index' => ListSettings::route('/'),
            'create' => CreateSetting::route('/create'),
            'edit' => EditSetting::route('/{record}/edit'),
        ];
    }
}
