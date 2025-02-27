<?php

namespace App\Livewire;

use App\Helper\CheckFilamentSellerUser;
use App\Models\Product;
use App\Models\Variant;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Livewire\Component;

class Barcode extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public Product|null $product = null;

    public Variant|null $variant = null;

    public  $barcodeTypes = [
        'C39' => 'C39',
        'C39+' => 'C39+',
        'C39E' => 'C39E',
        'C39E+' => 'C39E+',
        'C93' => 'C93',
        'C128' => 'C128',
        'C128A' => 'C128A',
        'C128B' => 'C128B',
        'CODABAR' => 'CODABAR',
    ];

    public function form(Form $form): Form
    {
        return $form->statePath('data')
            ->schema([
                Select::make('product_id')
                    ->label('Product Name')
                    ->searchable()
                    ->live()
                    ->afterStateUpdated(function (?string $state) {
                        if($state){
                            $this->product = Product::where('id',$state)->first();
                        }else{
                            $this->reset('product');
                        }
                    })
                    ->options(Product::where('seller_id',CheckFilamentSellerUser::getSellerId())->pluck('name','id')),

               Select::make('variant_id')->required()
                    ->hidden(fn(Get $get) => blank($get('product_id')) || !Product::where('id', $get('product_id'))->select('has_variant')->first()->has_variant)
                    ->searchable()->preload()->live()
                    ->afterStateUpdated(function ($state, Get $get,Set $set) {
                        if ($state) {
                            $this->variant = Variant::where('id', $state)->first();
                        }else{
                            $this->reset('variant');
                        }
                    })
                    ->options(function (Get $get) {
                        return Product::where('id', $get('product_id'))->first()->variants->pluck('name', 'id');
                    }),

                Select::make('type')->label('Type')
                    ->native(false)
                    ->live()
                    ->options($this->barcodeTypes),

                TextInput::make('total')
                    ->maxValue(50)
                    ->live()->integer()->required()
                    ->default(1),

            ])->columns(columns: 4);
    }

    public function render()
    {
        return view('livewire.barcode');
    }
}
