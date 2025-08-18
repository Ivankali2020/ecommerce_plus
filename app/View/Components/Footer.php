<?php

namespace App\View\Components;

use App\Helper\UserHelper;
use App\Models\Seller;
use Illuminate\View\Component;
use Illuminate\View\View;

class Footer extends Component
{
    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        $sellerId = UserHelper::getUserSellerId();
        $seller = Seller::with(['payments.media', 'media'])->find($sellerId);

        return view('components.footer', [
            'seller' => $seller,
        ]);
    }
}
