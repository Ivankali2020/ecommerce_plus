<?php

namespace App\Livewire;

use App\Helper\CheckFilamentSellerUser;
use App\Models\Product;
use App\Models\Variant;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Livewire\Component;

class Barcode extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public ?Product $product = null;

    public ?Variant $variant = null;

    public $barcodeTypes = [
        'C39' => 'C39',
        'C39+' => 'C39+',
        'C39E' => 'C39E',
        'C39E+' => 'C39E+',
        'C93' => 'C93',
        'C128' => 'C128',
        'C128A' => 'C128A',
        'C128B' => 'C128B',
        'CODABAR' => 'CODABAR',
        'QRCODE' => 'QRCODE',
    ];

    public function form(Schema $schema): Schema
    {
        return $schema->statePath('data')
            ->components([
                Select::make('product_id')
                    ->label(__('filament/resources/barcode-resource.labels.product_name'))
                    ->searchable()
                    ->live()
                    ->afterStateUpdated(function (?string $state) {
                        if ($state) {
                            $this->product = Product::where('id', $state)->first();
                        } else {
                            $this->reset('product');
                        }
                    })
                    ->options(Product::where('seller_id', CheckFilamentSellerUser::getSellerId())->pluck('name', 'id')),

                Select::make('variant_id')->required()
                    ->label(__('filament/resources/barcode-resource.labels.variant'))
                    ->hidden(fn (Get $get) => blank($get('product_id')) || ! Product::where('id', $get('product_id'))->select('has_variant')->first()->has_variant)
                    ->searchable()->preload()->live()
                    ->afterStateUpdated(function ($state, Get $get, Set $set) {
                        if ($state) {
                            $this->variant = Variant::where('id', $state)->first();
                        } else {
                            $this->reset('variant');
                        }
                    })
                    ->options(function (Get $get) {
                        return Product::where('id', $get('product_id'))->first()->variants->pluck('name', 'id');
                    }),

                Select::make('type')
                    ->label(__('filament/resources/barcode-resource.labels.type'))
                    ->native(false)
                    ->live()
                    ->options($this->barcodeTypes),

                TextInput::make('total')
                    ->label(__('filament/resources/barcode-resource.labels.total'))
                    ->maxValue(50)
                    ->live()->integer()->required()
                    ->default(1),

                TextInput::make('width_mm')
                    ->label('Width (mm)')
                    ->numeric()
                    ->live()
                    ->default(50)
                    ->minValue(20)
                    ->maxValue(200)
                    ->suffix('mm'),

                TextInput::make('height_mm')
                    ->label('Height (mm)')
                    ->numeric()
                    ->live()
                    ->default(30)
                    ->minValue(10)
                    ->maxValue(100)
                    ->suffix('mm'),

                TextInput::make('barcode_width')
                    ->label('Barcode Width')
                    ->numeric()
                    ->live()
                    ->default(2)
                    ->minValue(1)
                    ->maxValue(10)
                    ->helperText('Width multiplier for barcode bars'),

                TextInput::make('barcode_height')
                    ->label('Barcode Height')
                    ->numeric()
                    ->live()
                    ->default(43)
                    ->minValue(10)
                    ->maxValue(200)
                    ->helperText('Height of barcode bars in pixels'),

            ])->columns(columns: 4);
    }

    public function render()
    {
        return view('livewire.barcode');
    }
}
