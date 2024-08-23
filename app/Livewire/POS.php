<?php

namespace App\Livewire;

use App\Models\Category;
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
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Support\Contracts\TranslatableContentDriver;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
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


    public int $categoryId = 0;
    public int $discount = 0;

    public function changePriceDiscount($productId)
    {
        $this->data = collect($this->data)->map(function ($q) use ($productId){
            if ($q['product_id'] == $productId){
                $discountPrice = self::calculateDiscountPrice($q['price'],$this->discount);
                $q['discount'] = $this->discount;
                $q['discount_price'] = $discountPrice;
                $q['sub_price'] = $q['quantity'] * $discountPrice;
            }
            return $q;
        })->toArray();
    }

     static public function calculateDiscountPrice($price,$discount) : int
    {
        return ($price - $discount / 100 * $price);
    }

    #[Computed]
    public function products()
    {
       return Product::where('seller_id',Auth::guard('seller')->user()->parent_id ?? Auth::guard('seller')->id())
                    ->with('category','brand','exchange')
                    ->when($this->categoryId,function ($q){
                       return $q->where('category_id',$this->categoryId);
                    })->get();
    }

    public function filterCategory($categoryId)
    {
       $this->categoryId = $categoryId;
       Notification::make()->success()->title($this->products->count().' products found')->duration(500)->send();
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
                if (count($this->data) > 0){

                    if( collect($this->data)->where('product_id',$arguments['data']['id'])->count() > 0){
                        $this->data = collect($this->data)->map(function ($q) use ($arguments){
                            if ($q['product_id'] == $arguments['data']['id']){
                                $q['quantity'] += 1;
                                $q['sub_price'] = $q['quantity'] * ($q['discount'] > 0 ? $q['discount_price'] : $q['price']);
                            }
                            return $q;
                        })->toArray();
                        return;
                    }
                }

                $this->data[] = [
                    'product_id' => $arguments['data']['id'],
                    'name' => $arguments['data']['name'],
                    'category_name' => $arguments['data']['category']['name'],
                    'discount' =>  $arguments['data']['discount'],
                    'discount_price' => $arguments['data']['discount_price'],
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
            ->color('success')->outlined();
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
