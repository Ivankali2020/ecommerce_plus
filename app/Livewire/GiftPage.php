<?php

namespace App\Livewire;

use App\Models\Gift;
use App\Models\User;
use App\Services\PointService;
use Exception;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Gift Exchange')]
class GiftPage extends Component
{
    use WithPagination;

    public $selectedGift = null;

    public $showExchangeModal = false;

    public $deliveryAddress = '';

    public $exchangeNotes = '';

    public $search = '';

    public $sortBy = 'points_asc'; // points_asc, points_desc, name_asc, name_desc

    protected $pointService;

    public function boot(PointService $pointService)
    {
        $this->pointService = $pointService;
    }

    protected $rules = [
        'deliveryAddress' => 'required|string|max:500',
        'exchangeNotes' => 'nullable|string|max:255',
    ];

    public function mount()
    {
        // Set default delivery address from user's address
        if (Auth::check() && Auth::user()->addresses->isNotEmpty()) {
            $this->deliveryAddress = Auth::user()->addresses->first()->address;
        }
    }

    public function openExchangeModal($giftId)
    {
        if (! Auth::check()) {
            Notification::make()
                ->title('Login Required')
                ->body(__('gifts.messages.login_required_exchange'))
                ->warning()
                ->send();

            return;
        }

        $this->selectedGift = Gift::findOrFail($giftId);
        $this->showExchangeModal = true;
    }

    public function closeExchangeModal()
    {
        $this->selectedGift = null;
        $this->showExchangeModal = false;
        $this->deliveryAddress = '';
        $this->exchangeNotes = '';
        $this->resetValidation();
    }

    public function confirmExchange()
    {
        $this->validate();

        $user = Auth::user();

        if (! $this->selectedGift) {
            Notification::make()
                ->title('Error')
                ->body(__('gifts.messages.gift_not_found'))
                ->danger()
                ->send();

            return;
        }

        try {
            // Use the PointService to handle the exchange
            $result = $this->pointService->exchangeForGift(
                $user,
                $this->selectedGift,
                $this->deliveryAddress
            );

            // Update exchange notes if provided
            if ($this->exchangeNotes) {
                $result['exchange']->update(['notes' => $this->exchangeNotes]);
            }

            Notification::make()
                ->title('Gift Exchange Successful')
                ->body(__('gifts.messages.exchange_success', [
                    'points' => number_format($this->selectedGift->points_required),
                    'gift_name' => $this->selectedGift->name,
                ]))
                ->success()
                ->send();

            $this->closeExchangeModal();

            // Refresh user data to show updated points
            $this->dispatch('user-points-updated');
        } catch (Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function getUserPoints()
    {
        return Auth::check() ? Auth::user()->points : 0;
    }

    public function getPointsSummary()
    {
        return $this->pointService->getPointsSummary(Auth::user());
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Gift::with(['seller', 'giftExchanges'])
            ->active()
            ->inStock();

        $gifts = $query->paginate(12);

        return view('livewire.gift-page', [
            'gifts' => $gifts,
            'userPoints' => $this->getUserPoints(),
            'pointsSummary' => $this->getPointsSummary(),
        ]);
    }
}
