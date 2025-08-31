<?php

namespace App\Observers;

use App\Helper\ReturnStatus;
use App\Models\OrderReturnProduct;
use Illuminate\Support\Facades\Log;

class OrderReturnProductObserver
{
    /**
     * Handle the OrderReturnProduct "created" event.
     */
    public function created(OrderReturnProduct $orderReturnProduct): void
    {
        // Check if is_restocked is true on creation and order return status is completed
        if ($orderReturnProduct->is_restocked && $orderReturnProduct->quantity_approved > 0) {
            if ($this->isOrderReturnCompleted($orderReturnProduct)) {
                $this->handleRestocking($orderReturnProduct);
            }
        }

        // Update order product if quantity_approved > 0 and order return is completed
        if ($orderReturnProduct->quantity_approved > 0 && $this->isOrderReturnCompleted($orderReturnProduct)) {
            $this->updateOrderProduct($orderReturnProduct);
        }
    }

    /**
     * Handle the OrderReturnProduct "updated" event.
     */
    public function updated(OrderReturnProduct $orderReturnProduct): void
    {
        // Check if is_restocked was changed from false to true
        if ($orderReturnProduct->wasChanged('is_restocked') && $orderReturnProduct->is_restocked) {
            if ($this->isOrderReturnCompleted($orderReturnProduct)) {
                $this->handleRestocking($orderReturnProduct);
            }
        }

        // Check if quantity_approved was updated
        if ($orderReturnProduct->wasChanged('quantity_approved')) {
            if ($this->isOrderReturnCompleted($orderReturnProduct)) {
                $this->handleQuantityApprovedChange($orderReturnProduct);
            }
        }
    }

    /**
     * Handle the OrderReturnProduct "deleted" event.
     */
    public function deleted(OrderReturnProduct $orderReturnProduct): void
    {
        Log::info('OrderReturnProduct deleted event triggered', [
            'order_return_product_id' => $orderReturnProduct->id,
            'quantity_approved' => $orderReturnProduct->quantity_approved,
            'is_restocked' => $orderReturnProduct->is_restocked,
        ]);

        // If a return product is deleted, we should potentially reverse the order_products updates
        if ($orderReturnProduct->quantity_approved > 0) {
            Log::info('Calling reverseOrderProductUpdates for OrderReturnProduct', [
                'order_return_product_id' => $orderReturnProduct->id,
            ]);
            $this->reverseOrderProductUpdates($orderReturnProduct);
        }
    }

    /**
     * Handle the OrderReturnProduct "restored" event.
     */
    public function restored(OrderReturnProduct $orderReturnProduct): void
    {
        // Re-apply the order product updates if restored
        if ($orderReturnProduct->quantity_approved > 0) {
            $this->updateOrderProduct($orderReturnProduct);
        }
    }

    /**
     * Handle the OrderReturnProduct "force deleted" event.
     */
    public function forceDeleted(OrderReturnProduct $orderReturnProduct): void
    {
        // Same as deleted
        if ($orderReturnProduct->quantity_approved > 0) {
            $this->reverseOrderProductUpdates($orderReturnProduct);
        }
    }

    /**
     * Handle restocking when is_restocked is set to true
     */
    protected function handleRestocking(OrderReturnProduct $orderReturnProduct): void
    {
        if ($orderReturnProduct->quantity_approved <= 0) {
            return;
        }

        $orderProduct = $orderReturnProduct->orderProduct;
        if (! $orderProduct) {
            return;
        }

        // Increment product quantity based on variant or main product
        if ($orderProduct->variant_id && $orderProduct->variant) {
            // Restock variant
            $orderProduct->variant()->increment('quantity', $orderReturnProduct->quantity_approved);
        } elseif ($orderProduct->product) {
            // Restock main product
            $orderProduct->product()->increment('quantity', $orderReturnProduct->quantity_approved);
        }
    }

    /**
     * Handle quantity_approved changes - update order product and adjust stock if restocked
     */
    protected function handleQuantityApprovedChange(OrderReturnProduct $orderReturnProduct): void
    {
        $oldQuantityApproved = $orderReturnProduct->getOriginal('quantity_approved') ?? 0;
        $newQuantityApproved = $orderReturnProduct->quantity_approved;
        $quantityDifference = $newQuantityApproved - $oldQuantityApproved;

        // Update the order product
        $this->updateOrderProduct($orderReturnProduct);

        // If the item is restocked, adjust the product quantity
        if ($orderReturnProduct->is_restocked) {
            $this->adjustProductQuantity($orderReturnProduct, $quantityDifference);
        }
    }

    /**
     * Adjust product quantity based on approved quantity changes
     */
    protected function adjustProductQuantity(OrderReturnProduct $orderReturnProduct, int $quantityDifference): void
    {
        if ($quantityDifference == 0) {
            return;
        }

        $orderProduct = $orderReturnProduct->orderProduct;
        if (! $orderProduct) {
            return;
        }

        // Determine whether to increment or decrement
        if ($quantityDifference > 0) {
            // Quantity approved increased - increment stock
            if ($orderProduct->variant_id && $orderProduct->variant) {
                $orderProduct->variant()->increment('quantity', $quantityDifference);
            } elseif ($orderProduct->product) {
                $orderProduct->product()->increment('quantity', $quantityDifference);
            }
        } elseif ($quantityDifference < 0) {
            // Quantity approved decreased - decrement stock
            $decrementAmount = abs($quantityDifference);
            if ($orderProduct->variant_id && $orderProduct->variant) {
                $orderProduct->variant()->decrement('quantity', $decrementAmount);
            } elseif ($orderProduct->product) {
                $orderProduct->product()->decrement('quantity', $decrementAmount);
            }
        }
    }

    /**
     * Update order_products table when return product is approved
     */
    protected function updateOrderProduct(OrderReturnProduct $orderReturnProduct): void
    {
        $orderProduct = $orderReturnProduct->orderProduct;
        if (! $orderProduct) {
            return;
        }

        // Calculate total returned quantity and refunded amount for this order product
        // across all return products
        $totalReturnedQuantity = $orderProduct->returnProducts()
            ->whereHas('orderReturn', function ($query) {
                $query->where('status', ReturnStatus::Completed);
            })
            ->sum('quantity_approved');

        $totalRefundedAmount = $orderProduct->returnProducts()
            ->whereHas('orderReturn', function ($query) {
                $query->where('status', ReturnStatus::Completed);
            })
            ->sum('total_refund_amount');

        // Update order product with recalculated totals
        $orderProduct->update([
            'is_returned' => $totalReturnedQuantity > 0,
            'returned_quantity' => $totalReturnedQuantity,
            'total_refunded_amount' => $totalRefundedAmount,
        ]);
    }

    /**
     * Reverse order product updates when return product is deleted
     */
    protected function reverseOrderProductUpdates(OrderReturnProduct $orderReturnProduct): void
    {
        Log::info('reverseOrderProductUpdates called', [
            'order_return_product_id' => $orderReturnProduct->id,
            'order_product_id' => $orderReturnProduct->order_product_id,
        ]);

        $orderProduct = $orderReturnProduct->orderProduct;
        if (! $orderProduct) {
            Log::warning('Order product not found for return product', [
                'order_return_product_id' => $orderReturnProduct->id,
            ]);

            return;
        }

        Log::info('Order product found, proceeding with reversal', [
            'order_product_id' => $orderProduct->id,
            'current_returned_quantity' => $orderProduct->returned_quantity,
            'current_total_refunded_amount' => $orderProduct->total_refunded_amount,
        ]);

        // Recalculate totals from remaining return products (excluding the deleted one)
        $totalReturnedQuantity = $orderProduct->returnProducts()
            ->where('id', '!=', $orderReturnProduct->id) // Exclude the current/deleted one
            ->whereHas('orderReturn', function ($query) {
                $query->where('status', ReturnStatus::Completed);
            })
            ->sum('quantity_approved');

        $totalRefundedAmount = $orderProduct->returnProducts()
            ->where('id', '!=', $orderReturnProduct->id) // Exclude the current/deleted one
            ->whereHas('orderReturn', function ($query) {
                $query->where('status', ReturnStatus::Completed);
            })
            ->sum('total_refund_amount');

        Log::info('Calculated new totals', [
            'new_returned_quantity' => $totalReturnedQuantity,
            'new_refunded_amount' => $totalRefundedAmount,
        ]);

        // Update order product with recalculated totals
        $orderProduct->update([
            'is_returned' => $totalReturnedQuantity > 0,
            'returned_quantity' => $totalReturnedQuantity,
            'total_refunded_amount' => $totalRefundedAmount,
        ]);

        Log::info('Order product updated');

        // If was restocked, reverse the stock increment
        if ($orderReturnProduct->is_restocked && $orderReturnProduct->quantity_approved > 0) {
            Log::info('Reversing stock increment', [
                'quantity_to_decrement' => $orderReturnProduct->quantity_approved,
                'variant_id' => $orderProduct->variant_id,
            ]);

            if ($orderProduct->variant_id && $orderProduct->variant) {
                $orderProduct->variant()->decrement('quantity', $orderReturnProduct->quantity_approved);
                Log::info('Decremented variant stock');
            } elseif ($orderProduct->product) {
                $orderProduct->product()->decrement('quantity', $orderReturnProduct->quantity_approved);
                Log::info('Decremented product stock');
            }
        }

        Log::info('reverseOrderProductUpdates completed');
    }

    /**
     * Check if the parent OrderReturn status is completed
     */
    protected function isOrderReturnCompleted(OrderReturnProduct $orderReturnProduct): bool
    {
        return $orderReturnProduct->orderReturn
            && $orderReturnProduct->orderReturn->status === ReturnStatus::Completed;
    }
}
