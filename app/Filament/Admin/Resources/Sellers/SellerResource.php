<?php

namespace App\Filament\Admin\Resources\Sellers;

use App\Filament\Admin\Resources\Sellers\Pages\CreateSeller;
use App\Filament\Admin\Resources\Sellers\Pages\EditSeller;
use App\Filament\Admin\Resources\Sellers\Pages\ListSellers;
use App\Filament\Admin\Resources\Sellers\RelationManagers\SellerAddressesRelationManager;
use App\Filament\Admin\Resources\Sellers\RelationManagers\SellersRelationManager;
use App\Models\Seller;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Auth\Notifications\VerifyEmail;
use Filament\Facades\Filament;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;

class SellerResource extends Resource
{
    protected static ?string $model = Seller::class;

    protected static string|\UnitEnum|null $navigationGroup = 'User Section';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()->schema([
                    TextInput::make('name')
                        ->label(__('filament/resources/staff-resource.form.name'))
                        ->required()
                        ->maxLength(255),

                    TextInput::make('email')
                        ->label(__('filament/resources/staff-resource.form.email'))
                        ->email()
                        ->required()
                        ->maxLength(255),

                    TextInput::make('phone')
                        ->label(__('filament/resources/staff-resource.form.phone'))
                        ->maxLength(255),

                    TextInput::make('password')
                        ->label(__('filament/resources/staff-resource.form.password'))
                        ->password()
                        ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                        ->formatStateUsing(fn ($state) => $state)
                        ->revealable()->autocomplete()
                        ->required(fn (Page $livewire) => $livewire instanceof CreateRecord)
                        ->maxLength(255),

                    RichEditor::make('description')
                        ->required()->hidden(Filament::getAuthGuard() == 'seller')
                        ->maxLength(16777215)
                        ->columnSpanFull(),
                ])->columns(2),

                Group::make()->schema([
                    SpatieMediaLibraryFileUpload::make('logo')
                        ->collection('logos')->imageEditor()->label('Photo')
                        ->image()->rules('required'),

                    SpatieMediaLibraryFileUpload::make('cover_photo')
                        ->multiple()
                        ->collection('cover_photos')->imageEditor()->hidden(Filament::getAuthGuard() == 'seller')
                        ->image()->rules('required'),

                    Select::make('roles')
                        ->label(__('filament/resources/staff-resource.form.role'))
                        ->relationship('roles', 'name', modifyQueryUsing: fn ($query) => $query->where('guard_name', 'seller'))
                        ->multiple()
                        ->preload()
                        ->searchable(),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('logo')->collection('logos')->label('Photo'),
                SpatieMediaLibraryImageColumn::make('cover_photo')
                    ->hidden(Filament::getAuthGuard() == 'seller')
                    ->collection('cover_photos'),
                TextColumn::make('roles.name')
                    ->label(__('filament/resources/staff-resource.form.role'))
                    ->badge(),
                TextColumn::make('name')
                    ->label(__('filament/resources/staff-resource.form.name'))
                    ->searchable(),
                TextColumn::make('email')
                    ->label(__('filament/resources/staff-resource.form.email'))
                    ->searchable(),
                TextColumn::make('phone')
                    ->label(__('filament/resources/staff-resource.form.phone'))
                    ->searchable(),

                TextColumn::make('email_verified_at')
                    ->label('Email Verified')
                    ->dateTime('d m Y H:i'),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('verified')
                    ->label('Email Verified')
                    ->query(fn (Builder $query) => $query->whereNotNull('email_verified_at')),
                Filter::make('unverified')
                    ->label('Email Not Verified')
                    ->query(fn (Builder $query) => $query->whereNull('email_verified_at')),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('resend_verification_email')
                    ->label('Resend Verification Email')
                    ->icon('heroicon-o-envelope')
                    ->authorize(fn (Seller $record) => ! $record->hasVerifiedEmail())
                    ->action(function (Seller $record) {
                        $notification = new VerifyEmail;
                        $notification->url = filament()->getPanel('seller')->getVerifyEmailUrl($record);
                        $record->notify($notification);

                        Notification::make()
                            ->title('Verification email has been resent.')
                            ->send();
                    })
                    ->requiresConfirmation(),
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
            SellerAddressesRelationManager::class,
            SellersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSellers::route('/'),
            'create' => CreateSeller::route('/create'),
            'edit' => EditSeller::route('/{record}/edit'),
        ];
    }
}
