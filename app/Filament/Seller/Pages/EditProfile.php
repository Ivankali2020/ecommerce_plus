<?php

namespace App\Filament\Seller\Pages;


use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Illuminate\Support\Js;

class EditProfile extends \Filament\Pages\Auth\EditProfile implements HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.seller.pages.edit-profile';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(
            auth()->user()->attributesToArray()
        );
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                $this->getNameFormComponent(),
                $this->getEmailFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
               SpatieMediaLibraryFileUpload::make('logo')
                    ->collection('logos')->imageEditor()
                    ->image()->rules('required|mimes:jpeg,png,jpg|dimensions:ratio=1/1'),

                SpatieMediaLibraryFileUpload::make('cover_photo')
                    ->multiple()->maxFiles(5)->label('Cover Photos')
                    ->collection('cover_photos')->imageEditor()
                    ->image()->rules('required|mimes:jpeg,png,jpg|dimensions:ratio=16/9'),
            ])
            ->statePath('data')
            ->model(auth()->user());
    }

    public function update()
    {
        $this->save();
        $this->getSavedNotification();
    }

    protected function getFormActions(): array
    {
        return [

            Action::make('back')
                ->label(__('filament-panels::pages/auth/edit-profile.actions.cancel.label'))
                ->alpineClickHandler('document.referrer ? window.history.back() : (window.location.href = ' . Js::from(filament()->getUrl()) . ')')
                ->color('gray'),
            Action::make('Update')
                ->color('primary')
                ->submit('Update'),
        ];
    }


}
