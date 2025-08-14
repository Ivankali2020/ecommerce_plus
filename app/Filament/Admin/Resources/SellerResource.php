<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\SellerResource\Pages;
use App\Filament\Admin\Resources\SellerResource\RelationManagers;
use App\Models\Seller;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Auth\VerifyEmail;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;

class SellerResource extends Resource
{
    protected static ?string $model = Seller::class;

    protected static ?string $navigationGroup = 'User Section';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()->schema([
                    Forms\Components\TextInput::make('name')
                        ->label(__('filament/resources/staff-resource.form.name'))
                        ->required()
                        ->maxLength(255),

                    Forms\Components\TextInput::make('email')
                        ->label(__('filament/resources/staff-resource.form.email'))
                        ->email()
                        ->required()
                        ->maxLength(255),

                    Forms\Components\TextInput::make('phone')
                        ->label(__('filament/resources/staff-resource.form.phone'))
                        ->maxLength(255),

                    Forms\Components\TextInput::make('password')
                        ->label(__('filament/resources/staff-resource.form.password'))
                        ->password()
                        ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                        ->formatStateUsing(fn ($state) => $state)
                        ->revealable()->autocomplete()
                        ->required(fn (Page $livewire) => $livewire instanceof CreateRecord)
                        ->maxLength(255),

                    Forms\Components\RichEditor::make('description')
                        ->required()->hidden(Filament::getAuthGuard() == 'seller')
                        ->maxLength(16777215)
                        ->columnSpanFull(),
                ])->columns(2),

                Forms\Components\Group::make()->schema([
                    Forms\Components\SpatieMediaLibraryFileUpload::make('logo')
                        ->collection('logos')->imageEditor()->label('Photo')
                        ->image()->rules('required'),

                    Forms\Components\SpatieMediaLibraryFileUpload::make('cover_photo')
                        ->multiple()
                        ->collection('cover_photos')->imageEditor()->hidden(Filament::getAuthGuard() == 'seller')
                        ->image()->rules('required'),

                    Forms\Components\Select::make('roles')
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
                Tables\Columns\SpatieMediaLibraryImageColumn::make('logo')->collection('logos')->label('Photo'),
                Tables\Columns\SpatieMediaLibraryImageColumn::make('cover_photo')
                    ->hidden(Filament::getAuthGuard() == 'seller')
                    ->collection('cover_photos'),
                Tables\Columns\TextColumn::make('roles.name')
                    ->label(__('filament/resources/staff-resource.form.role'))
                    ->badge(),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('filament/resources/staff-resource.form.name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label(__('filament/resources/staff-resource.form.email'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label(__('filament/resources/staff-resource.form.phone'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('email_verified_at')
                    ->label('Email Verified')
                    ->dateTime('d m Y H:i'),

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
                Tables\Filters\Filter::make('verified')
                    ->label('Email Verified')
                    ->query(fn (Builder $query) => $query->whereNotNull('email_verified_at')),
                Tables\Filters\Filter::make('unverified')
                    ->label('Email Not Verified')
                    ->query(fn (Builder $query) => $query->whereNull('email_verified_at')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('resend_verification_email')
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
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\SellerAddressesRelationManager::class,
            RelationManagers\SellersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSellers::route('/'),
            'create' => Pages\CreateSeller::route('/create'),
            'edit' => Pages\EditSeller::route('/{record}/edit'),
        ];
    }
}
