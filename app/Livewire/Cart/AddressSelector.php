<?php

namespace App\Livewire\Cart;

use App\Helper\UserHelper;
use App\Models\Delivery;
use App\Models\UserAddress;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Component;

class AddressSelector extends Component
{
    #[Locked]
    public $sellerId;

    public $selectedAddress = '';

    public $deliveryDetails = null;

    public function mount(): void
    {
        $this->sellerId = UserHelper::getUserSellerId();
    }

    #[Computed]
    public function addresses()
    {
        return UserAddress::where('user_id', auth('web')->id())->get();
    }

    public function updatedSelectedAddress(): void
    {
        $this->deliveryDetails = null;

        if ($this->selectedAddress) {
            $address = UserAddress::find($this->selectedAddress);

            if ($address) {
                // Find delivery option for this address location
                $delivery = Delivery::where('seller_id', $this->sellerId)
                    ->where('tenant_id', tenant('id'))
                    ->where('region_id', $address->region_id)
                    ->where('township_id', $address->township_id)
                    ->first();

                if ($delivery) {
                    $this->deliveryDetails = [
                        'delivery_id' => $delivery->id,
                        'fees' => $delivery->fees,
                        'is_cod' => $delivery->is_cod,
                        'is_pick_up' => $delivery->is_pick_up,
                        'remark' => $delivery->remark,
                        'region_name' => $address->region->mm_name,
                        'township_name' => $address->township->mm_name,
                    ];

                    // Dispatch events to update other components
                    $this->dispatch('delivery-fee-updated', fee: $delivery->fees);
                    $this->dispatch('delivery-updated', details: $this->deliveryDetails);
                } else {
                    // No delivery available for this location
                    $this->deliveryDetails = [
                        'delivery_id' => null,
                        'fees' => 0,
                        'is_cod' => false,
                        'is_pick_up' => false,
                        'remark' => 'Delivery not available for this location',
                        'region_name' => $address->region->mm_name,
                        'township_name' => $address->township->mm_name,
                        'unavailable' => true,
                    ];

                    // Dispatch events to update other components
                    $this->dispatch('delivery-fee-updated', fee: 0);
                    $this->dispatch('delivery-updated', details: $this->deliveryDetails);
                }
            }
        }

        $this->dispatch('address-updated', addressId: $this->selectedAddress, deliveryDetails: $this->deliveryDetails);
    }

    public function render()
    {
        return view('livewire.cart.address-selector');
    }
}
