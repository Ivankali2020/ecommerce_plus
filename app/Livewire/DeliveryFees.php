<?php

namespace App\Livewire;

use App\Helper\UserHelper;
use App\Models\Region;
use Livewire\Component;

class DeliveryFees extends Component
{
    public $regions;

    public $selectedRegion = null;

    public $townships;

    public $sellerId;

    public function mount()
    {
        $this->sellerId = UserHelper::getUserSellerId();
        $this->townships = collect();
        $this->loadRegions();
    }

    public function loadRegions()
    {
        // Get regions that have deliveries for this seller
        $this->regions = Region::whereHas('townships', function ($query) {
            $query->whereHas('deliveries', function ($subQuery) {
                $subQuery->where('seller_id', $this->sellerId);
            });
        })->with(['townships' => function ($query) {
            $query->whereHas('deliveries', function ($subQuery) {
                $subQuery->where('seller_id', $this->sellerId);
            })->with(['deliveries' => function ($deliveryQuery) {
                $deliveryQuery->where('seller_id', $this->sellerId);
            }]);
        }])->get();
    }

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
