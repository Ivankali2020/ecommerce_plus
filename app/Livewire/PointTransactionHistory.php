<?php

namespace App\Livewire;

use App\Models\PointTransaction;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class PointTransactionHistory extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $typeFilter = '';

    public string $sortBy = 'created_at';

    public string $sortDirection = 'desc';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedTypeFilter(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->typeFilter = '';
        $this->resetPage();
    }

    #[Computed]
    public function pointTransactions()
    {
        return PointTransaction::query()
            ->where('user_id', Auth::id())
            ->when($this->search, function ($query) {
                $query->where('description', 'like', '%'.$this->search.'%')
                    ->orWhere('reference_type', 'like', '%'.$this->search.'%');
            })
            ->when($this->typeFilter, function ($query) {
                $query->where('type', $this->typeFilter);
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(15);
    }

    #[Computed]
    public function totalEarned(): int
    {
        return PointTransaction::where('user_id', Auth::id())
            ->where('type', 'earned')
            ->sum('points');
    }

    #[Computed]
    public function totalSpent(): int
    {
        return abs(PointTransaction::where('user_id', Auth::id())
            ->where('type', 'spent')
            ->sum('points'));
    }

    #[Computed]
    public function currentBalance(): int
    {
        return Auth::user()->points ?? 0;
    }

    public function render()
    {
        return view('livewire.point-transaction-history');
    }
}
