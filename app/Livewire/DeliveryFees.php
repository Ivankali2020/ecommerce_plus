<?php

namespace App\Livewire;

use App\Helper\UserHelper;
use App\Models\Region;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Cache;

class DeliveryFees extends Component
{

    public $selectedRegion = null;

    public $townships;

    public $sellerId;


    public function mount()
    {
        $this->sellerId = UserHelper::getUserSellerId();
        $this->townships = collect();
    }

    #[Computed]
    public function regions()
    {
        $sellerId = $this->sellerId;
        return Cache::remember("delivery_regions_{$sellerId}", 180, function () use ($sellerId) {
            return Region::whereHas('townships', function ($query) use ($sellerId) {
                $query->whereHas('deliveries', function ($subQuery) use ($sellerId) {
                    $subQuery->where('seller_id', $sellerId);
                });
            })->with(['townships' => function ($query) use ($sellerId) {
                $query->whereHas('deliveries', function ($subQuery) use ($sellerId) {
                    $subQuery->where('seller_id', $sellerId);
                })->with(['deliveries' => function ($deliveryQuery) use ($sellerId) {
                    $deliveryQuery->where('seller_id', $sellerId);
                }]);
            }])->get();
        });
    }
    // No need for loadRegions(), handled by computed property

    public function selectRegion($regionId)
    {
        $this->selectedRegion = $regionId;
    $region = $this->regions->firstWhere('id', $regionId);
        $this->townships = $region ? $region->townships : collect();
    }

    public function render()
    {
        return view('livewire.delivery-fees');
    }
}
