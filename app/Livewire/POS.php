<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\Variant;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Support\Contracts\TranslatableContentDriver;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\View\View;
use Livewire\Component;

class POS extends Component implements HasActions,HasForms
{
    use InteractsWithActions,InteractsWithForms;

    public function categoryAction(): Action
    {
        return Action::make('category')
            ->form([
                TextInput::make('name')
            ])
            ->size('sm')
            ->modalWidth('md')
            ->modalHeading('အမျိုးအစားအလိုက်ရှာရန်')
            ->color('success')->outlined()
            ->action(function (array $arguments){
                dd($arguments);
            });
    }

    public ?array $data = [];

    public function create(): void
    {
        dd($this->form->getState());
    }

    public function addAction(): Action
    {
        return Action::make('add')
            ->size('sm')
            ->color('success')->outlined()
            ->action(function (array $arguments){
                $this->data[] = [
                    'product_id' => $arguments['data']['id'],
                    'name' => $arguments['data']['name'],
                    'quantity' => 1,
                    'price' => $arguments['data']['price'],
                    'sub_price' => $arguments['data']['price']
                ];
            });
    }

    public function variantsAction(): Action
    {
        return Action::make('variants')
            ->label('Choose')
            ->modalWidth('md')
            ->registerModalActions([
                $this->addAction()
            ])
            ->size('sm')
            ->modalContent(fn (Action $action,array $arguments): View => view(
                'livewire.variant-choose',
                [
                    'action' => $action,
                    'record' => Variant::where('product_id',$arguments['data']['id'])->with('product')->get()
                ],
            ))
            ->modalCancelAction(false)
            ->modalSubmitAction(false)
            ->color('success')->outlined()
            ->action(function (array $arguments){

            });
    }

    public function deleteProductAction(): Action
    {
        return Action::make('deleteProduct')
            ->label('')
            ->iconButton()->iconSize('sm')
            ->size('sm')
            ->icon('heroicon-m-trash')
            ->color('danger')->outlined()
            ->action(function (array $arguments){
                dd($arguments);
            });
    }


    public function render()
    {
        return view('livewire.p-o-s');
    }

    public function makeFilamentTranslatableContentDriver(): ?TranslatableContentDriver
    {
        // TODO: Implement makeFilamentTranslatableContentDriver() method.
    }
}
