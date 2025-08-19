<?php

namespace App\Filament\Seller\Pages;

use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Illuminate\Support\Js;

class EditProfile extends \Filament\Auth\Pages\EditProfile implements HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    // protected string $view = 'filament.seller.pages.edit-profile';

    public ?array $data = [];

    public function mount(): void
    {
        // dd(auth('seller')->user());
        $this->form->fill(
            auth('seller')->user()->attributesToArray()
        );
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getNameFormComponent(),
                $this->getEmailFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),

                TextInput::make('telegram_chat_id')
                    ->nullable()->string(),

                SpatieMediaLibraryFileUpload::make('logo')
                    ->collection('logos')->imageEditor()
                    ->image()->rules('required|mimes:jpeg,png,jpg,svg|dimensions:ratio=1/1'),

                SpatieMediaLibraryFileUpload::make('cover_photo')
                    ->multiple()->maxFiles(5)->label('Cover Photos')
                    ->collection('cover_photos')->imageEditor()
                    ->image()->rules('required|mimes:jpeg,png,jpg,webp'), // |dimensions:ratio=16/9
            ])
            ->statePath('data')
            ->model(auth('seller')->user());
    }

    public function update()
    {
        $this->save();
        $this->getSavedNotification();

        return redirect()->route('filament.seller.pages.dashboard');
    }

    // protected function getFormActions(): array
    // {
    //     return [

    //         Action::make('back')
    //             ->label(__('filament-panels::pages/auth/edit-profile.actions.cancel.label'))
    //             ->alpineClickHandler('document.referrer ? window.history.back() : (window.location.href = '.Js::from(filament()->getUrl()).')')
    //             ->color('gray'),
    //         Action::make('Update')
    //             ->color('primary')
    //             ->submit('Update'),
    //     ];
    // }
}
