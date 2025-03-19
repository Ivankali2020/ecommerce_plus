<?php

namespace App\Livewire\Profile;

use App\Models\UserAddress;
use App\Models\Region;
use App\Models\Township;
use Filament\Notifications\Notification;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Reactive;

class UserAddressForm extends Component
{
    public $selectedRegion = '';
    public $selectedTownship = '';
    public $address = '';
    public $phone = '';
    public $editId = null;

    public $regions;
    public $townships = [];
    public $addresses;

    protected $rules = [
        'selectedRegion' => 'required|exists:regions,id',
        'selectedTownship' => 'required|exists:townships,id',
        'address' => 'required|string|min:5',
        'phone' => 'nullable|string|min:9|max:11',
    ];

    public function mount()
    {
        $this->regions = Region::all();
        $this->loadAddresses();
    }

    public function loadAddresses()
    {
        $this->addresses = UserAddress::where('user_id', Auth::id())
            ->with(['region', 'township'])
            ->get();
    }

    public function updatedSelectedRegion($value)
    {
        $this->townships = Township::where('region_id', $value)->get();
        
        // Only reset the township when not in edit mode
        if ($this->editId === null) {
            $this->selectedTownship = '';
        }
    }

    public function edit($addressId)
    {
        $address = UserAddress::find($addressId);
        if ($address && $address->user_id === Auth::id()) {
            $this->editId = $addressId;
            $this->address = $address->address;
            $this->phone = $address->phone;
            
            // Set region first
            $this->selectedRegion = (string)$address->region_id;
            
            // Load townships for this region
            $this->townships = Township::where('region_id', $address->region_id)->get();
            
            // Set township ID as string to ensure type consistency
            $this->selectedTownship = (string)$address->township_id;
        }
    }

    public function cancelEdit()
    {
        $this->reset(['editId', 'selectedRegion', 'selectedTownship', 'address', 'phone']);
        $this->townships = [];
    }

    public function delete($addressId)
    {
        $address = UserAddress::where('id', $addressId)
            ->where('user_id', Auth::id())
            ->first();

            Notification::make()->title('Successfully Deleted !')->success()->send();

        if ($address) {
            $address->delete();
            $this->loadAddresses();
        }
    }

    public function save()
    {
        $this->validate();

        if ($this->editId) {
            UserAddress::where('id', $this->editId)
                ->where('user_id', Auth::id())
                ->update([
                    'region_id' => $this->selectedRegion,
                    'township_id' => $this->selectedTownship,
                    'address' => $this->address,
                    'phone' => $this->phone,
                ]);
        } else {
            if ($this->addresses->count() >= 3) {
                Notification::make()->title('Maximum 3 addresses allowed')->success()->send();
                return;
            }

            UserAddress::create([
                'user_id' => Auth::id(),
                'region_id' => $this->selectedRegion,
                'township_id' => $this->selectedTownship,
                'address' => $this->address,
                'phone' => $this->phone,
            ]);
        }
        Notification::make()->title('Successfully Done !')->success()->send();

        $this->cancelEdit();
        $this->loadAddresses();
    }

    public function render()
    {
        return view('livewire.profile.user-address-form');
    }
}
