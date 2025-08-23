<?php

namespace App\View\Components;

use App\Models\Seller;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\Component;
use Illuminate\View\View;

class Footer extends Component
{
    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        // $seller = Seller::with(['payments.media', 'media'])->find($sellerId);
        $tenant = tenant('id');
        $seller = Cache::remember("seller_{$tenant}", 60, function () use ($tenant) {
            return Seller::where('tenant_id', $tenant)->with(['payments.media', 'media' , 'setting'])->first();
        });

        

        return view('components.footer', [
            'seller' => $seller,
        ]);
    }
}
