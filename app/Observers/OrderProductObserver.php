<?php

namespace App\Observers;

use App\Models\OrderProduct;
use App\Models\Product;
use App\Models\Variant;

class OrderProductObserver
{
    /**
     * Handle the OrderProduct "created" event.
     */
    public function creating(OrderProduct $orderProduct): void
    {
        $product = $orderProduct->product;
        // if ($orderProduct->getOriginal('quantity') == null ){
        //
        // if ($product->has_variant){
        //     $variant = Variant::where('id',$orderProduct->variant_id)->first();
        //     $variant->decrement('quantity',$orderProduct->quantity);
        // }else{
        //     $product->decrement('quantity',$orderProduct->quantity);
        //     logger('logger from order product observer '.$product->quantity);
        // }
        // }
        // $product->decrement('quantity',$orderProduct->quantity);
        $orderProduct->profit = $orderProduct->discount_price > 0 ?
                                    ($orderProduct->discount_price * $orderProduct->quantity) - ($orderProduct->original_buy_price * $orderProduct->quantity) :
                                    ($product->price * $orderProduct->quantity) - ($orderProduct->original_buy_price * $orderProduct->quantity);

    }

    public function created(OrderProduct $orderProduct): void
    {
        $product = $orderProduct->product;
        if ($product->has_variant) {
            $variant = Variant::where('id', $orderProduct->variant_id)->first();
            $variant->preventObserver = true;
            $variant->decrement('quantity', $orderProduct->quantity);
            $variant->preventObserver = false;
        } else {
            // Set the preventObserver flag to true before decrementing
            $product->preventObserver = true;
            $product->decrement('quantity', $orderProduct->quantity);
            // Reset the flag after operation
            $product->preventObserver = false;
        }
    }

    public function updated(OrderProduct $orderProduct)
    {
        if ($orderProduct->getOriginal('quantity') != $orderProduct->quantity) { // && $orderProduct->getOriginal('product_id') == $orderProduct->product_id
            $product = Product::where('id', $orderProduct->product_id)->first();
            if ($orderProduct->getOriginal('quantity') > $orderProduct->quantity) {
                // if reduce quantity we will increment the original quantity
                $incrementQuantity = $orderProduct->getOriginal('quantity') - $orderProduct->quantity;
                if ($product->has_variant) {
                    $variant = Variant::where('id', $orderProduct->variant_id)->first();
                    $variant->increment('quantity', $incrementQuantity);
                } else {
                    $product->increment('quantity', $incrementQuantity);
                }
            } elseif ($orderProduct->getOriginal('quantity') < $orderProduct->quantity) {

                // if increase quantity we will decrement the original quantity
                $decrementQuantity = $orderProduct->quantity - $orderProduct->getOriginal('quantity');
                if ($product->has_variant) {
                    $variant = Variant::where('id', $orderProduct->variant_id)->first();
                    $variant->decrement('quantity', $decrementQuantity);
                } else {
                    $product->decrement('quantity', $decrementQuantity);
                }
            }

            $orderProduct->profit = $orderProduct->discount_price > 0 ?
            ($orderProduct->discount_price * $orderProduct->quantity) - ($orderProduct->original_buy_price * $orderProduct->quantity) :
            ($product->price * $orderProduct->quantity) - ($orderProduct->original_buy_price * $orderProduct->quantity);
        }
    }

    /**
     * Handle the OrderProduct "deleted" event.
     */
    public function deleted(OrderProduct $orderProduct): void
    {
        $product = $orderProduct->product;
        
        if ($product->has_variant) {
            $variant = Variant::where('id', $orderProduct->variant_id)->first();
            if ($variant) {
                $variant->preventObserver = true;
                $variant->increment('quantity', $orderProduct->quantity);
                $variant->preventObserver = false;
            }
        } else {
            // Set the preventObserver flag to true before incrementing
            $product->preventObserver = true;
            $product->increment('quantity', $orderProduct->quantity);
            // Reset the flag after operation
            $product->preventObserver = false;
        }
    }

    /**
     * Handle the OrderProduct "restored" event.
     */
    public function restored(OrderProduct $orderProduct): void
    {
        //
    }

    /**
     * Handle the OrderProduct "force deleted" event.
     */
    public function forceDeleted(OrderProduct $orderProduct): void
    {
        //
    }
}
