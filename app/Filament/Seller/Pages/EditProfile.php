<?php

namespace App\Filament\Seller\Pages;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Components\Section;
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
                    ->hint(function(){
                        return Action::make('optimize image')
                                                        ->hiddenLabel()
                                                        ->size('sm')
                                                        ->icon('heroicon-o-link')
                                                        ->color('green')
                                                        ->url('https://t.me/ivannoti_bot')
                                                        ->openUrlInNewTab();
                    })
                    ->suffix('TYPE : /getId ')
                    ->nullable()->string(),

                SpatieMediaLibraryFileUpload::make('logo')
                    ->collection('logos')->imageEditor()
                    ->image()
                    ->rules('required|mimes:jpeg,png,jpg,svg,webp,avif|dimensions:ratio=1/1|max:512'),

                SpatieMediaLibraryFileUpload::make('cover_photo')
                    ->multiple()->maxFiles(5)
                    ->collection('cover_photos')->imageEditor()
                    ->imageEditorAspectRatios([
                        '21:6', // The desired 21:9 aspect ratio
                        '21:9', // The desired 21:9 aspect ratio
                        '1:1',  // Other common aspect ratios
                        '4:3',
                    ])
                    ->image()->rules('required|mimes:jpeg,png,jpg,webp,avif|max:512'), // |dimensions:ratio=21/9

                Section::make()->description('512 kb ထက် များနေပါက Optimize လုပ်ပေးပါရန် ')
                                                ->afterHeader([
                                                    Action::make('optimize image')
                                                        ->size('sm')
                                                        ->color('green')
                                                        ->url('https://saruwakakun.com/tools/png-jpeg-to-webp/')
                                                        ->openUrlInNewTab()
                                                ])->columnSpanFull()
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
