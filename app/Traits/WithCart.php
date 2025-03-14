<?php

namespace App\Traits;

use App\Models\Cart;
use App\Models\Product as ProductModel;
use Filament\Forms\Components\Livewire;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

trait WithCart
{
    public function addToCart($productId, $variantId = null, $quantity = 1)
    {
        try {
            if (!Auth::check()) {
                return redirect()->route('login');
            }

            $product = ProductModel::where('id', $productId)
                ->with(['variants'])
                ->first();

            $variant = $variantId
                ? $product->variants->find($variantId)
                : ($product->has_variant ? $product->variants->first() : null);

            $price = $variant
                ? ($variant->discount > 0 ? $variant->mmk_discount_price : $variant->mmk_price)
                : ($product->discount > 0 ? $product->mmk_discount_price : $product->mmk_price);


            // Check existing cart item
            $existingCart = Cart::where([
                'user_id' => Auth::id(),
                'product_id' => $productId,
                'variant_id' => $variant?->id
            ])->first();

            $newQuantity = $existingCart ? $existingCart->quantity + 1 : $quantity;

            $this->dispatch('cartUpdated');
            // Check MOQ
            if ($newQuantity < ($variant?->MOQ ?? $product->MOQ)) {
                Notification::make()
                    ->title(__('messages.cart.min_order_not_met'))
                    ->body(__('messages.cart.min_order_quantity', ['quantity' => $product->MOQ]))
                    ->warning()
                    ->send();
                return;
            }
            // Check stock
            if ($newQuantity > ($variant?->quantity ?? $product->quantity)) {
                Notification::make()
                    ->title(__('messages.cart.out_of_stock'))
                    ->body(__('messages.cart.limited_stock', ['quantity' => $product->quantity]))
                    ->warning()
                    ->send();
                return;
            }

            // Update or create cart item
            Cart::updateOrCreate(
                [
                    'seller_id' => $product->seller_id,
                    'user_id' => Auth::id(),
                    'product_id' => $productId,
                    'price' => $price,
                    'variant_id' => $variant?->id
                ],
                [
                    'quantity' => $newQuantity,
                    'price' => $price,
                    'total' => $newQuantity * $price
                ]
            );

            if($existingCart){
                Notification::make()
                ->title(__('messages.cart.quantity_updated'))
                ->success()
                ->send();
            }else{
                Notification::make()
                ->title(__('messages.cart.product_added'))
                ->success()
                ->send();
            }



        } catch (\Exception $e) {
            logger()->error('Error adding to cart: ' . $e->getMessage());

            Notification::make()
                ->title('Failed to add product to cart')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}
