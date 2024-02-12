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
        if ($orderProduct->getOriginal('quantity') == null ){
            $product = Product::where('id',$orderProduct->product_id)->first();
            if ($product->has_variant){
                $variant = Variant::where('id',$orderProduct->variant_id)->first();
                $variant->decrement('quantity',$orderProduct->quantity);
            }else{
                $product->decrement('quantity',$orderProduct->quantity);
            }
        }

    }

    /**
     * Handle the OrderProduct "updated" event.
     */
    public function saving(OrderProduct $orderProduct): void
    {
        //add original price and currency
//        $orderProduct->original_price = $orderProduct->price;
//        $orderProduct->original_discount_price = $orderProduct->discount_price;
//        $orderProduct->exchange_rate = $orderProduct->product->exchange->rate;
//        $orderProduct->currency_id = $orderProduct->product->exchange->currency_id;
//        $orderProduct->update();

    }

    public function updated(OrderProduct $orderProduct)
    {
        if ($orderProduct->getOriginal('quantity') != $orderProduct->quantity && $orderProduct->getOriginal('product_id') == $orderProduct->product_id) {
            $product = Product::where('id', $orderProduct->product_id)->first();
            if ($orderProduct->getOriginal('quantity') > $orderProduct->quantity) {
                //if reduce quantity we will increment the original quantity
                $incrementQuantity = $orderProduct->getOriginal('quantity') - $orderProduct->quantity;
                if ($product->has_variant) {
                    $variant = Variant::where('id', $orderProduct->variant_id)->first();
                    $variant->increment('quantity', $incrementQuantity);
                } else {
                    $product->increment('quantity', $incrementQuantity);
                }
            } else if ($orderProduct->getOriginal('quantity') < $orderProduct->quantity) {

                //if increase quantity we will decrement the original quantity
                $decrementQuantity = $orderProduct->quantity - $orderProduct->getOriginal('quantity');
                if ($product->has_variant) {
                    $variant = Variant::where('id', $orderProduct->variant_id)->first();
                    $variant->decrement('quantity', $decrementQuantity);
                } else {
                    $product->decrement('quantity', $decrementQuantity);
                }
            }
        }
    }

//    public function updated(OrderProduct $orderProduct): void
//    {
//        if ($orderProduct->getOriginal('quantity') == $orderProduct->quantity) {
//            //add original price and currency
//            $orderProduct->original_price = $orderProduct->price;
//            $orderProduct->original_discount_price = $orderProduct->discount_price;
//            $orderProduct->exchange_rate = $orderProduct->product->exchange->rate;
//            $orderProduct->currency_id = $orderProduct->product->exchange->currency_id;
//            $orderProduct->update();
//        }
//    }

    /**
     * Handle the OrderProduct "deleted" event.
     */
    public function deleted(OrderProduct $orderProduct): void
    {
        //
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
