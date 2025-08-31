<?php

namespace App\Services;

use App\Helper\ReturnStatus;
use App\Models\Order;
use App\Models\OrderReturn;
use Illuminate\Support\Facades\DB;

class ReturnService
{
    public function createReturn(Order $order, string $reason, array $returnItems = [], ?int $userId = null, array $adminControls = []): OrderReturn
    {
        // Validate that the order can be returned
        if (! $order->canDoRet()) {
            throw new \InvalidArgumentException('This order is not eligible for return');
        }

        // Check if there's already an active return for this order
        if ($order->hasActiveReturn()) {
            throw new \InvalidArgumentException('This order already has an active return request');
        }

        return DB::transaction(function () use ($order, $reason, $returnItems, $userId, $adminControls) {
            $orderReturn = new OrderReturn;

            $orderReturn->fill([
                'invoice_number' => $orderReturn->generateInvoiceNumber(),
                'order_id' => $order->id,
                'seller_id' => $order->seller_id,
                'tenant_id' => $order->tenant_id,
                'user_id' => $userId ?? $order->user_id,
                'status' => ReturnStatus::Completed,
                'reason' => $reason,
                'requested_at' => now(),
                // Add admin controls
                'restocking_decision' => $adminControls['restocking_decision'] ?? null,
                'should_restock' => $adminControls['should_restock'] ?? false,
                'admin_notes' => $adminControls['admin_notes'] ?? null,
                'total_refund_amount' => $adminControls['total_refund_amount'] ?? 0,
                'payment_slip' => $adminControls['payment_slip'] ?? null,
            ]);

            $orderReturn->save();

            // Create return products - if no specific items provided, return all
            if (empty($returnItems)) {
                foreach ($order->products as $orderProduct) {
                    if ($orderProduct->canDoRet()) {
                        $orderReturn->returnProducts()->create([
                            'order_product_id' => $orderProduct->id,
                            'quantity_requested' => $orderProduct->getAvailableReturnQuantity(),
                            'individual_reason' => $reason,
                        ]);
                    }
                }
            } else {
                // Create return products for specific items
                foreach ($returnItems as $item) {
                    $orderProduct = $order->products()->find($item['order_product_id']);

                    if (! $orderProduct || ! $orderProduct->canDoRet()) {
                        throw new \InvalidArgumentException('Invalid order product or product cannot be returned');
                    }

                    $requestedQuantity = min($item['quantity'], $orderProduct->getAvailableReturnQuantity());

                    $orderReturn->returnProducts()->create([
                        'is_restocked' => isset($adminControls['should_restock']) ? $adminControls['should_restock'] : ($item['is_restocked'] ?? false),
                        'order_product_id' => $orderProduct->id,
                        'quantity_requested' => $requestedQuantity,
                        'quantity_approved' => $requestedQuantity,
                        'unit_refund_amount' => $item['unit_refund_amount'],
                        'total_refund_amount' => $item['total_refund_amount'],
                        'individual_reason' => $item['reason'] ?? $reason,
                    ]);

                    // if (isset($adminControls['auto_complete']) && $adminControls['auto_complete']) {
                    //     // update order products
                    //     $orderProduct->update([
                    //         'is_returned' => true,
                    //         'returned_quantity' => $orderProduct->returned_quantity + $requestedQuantity,
                    //         'total_refunded_amount' => $orderProduct->total_refunded_amount + ($orderProduct->price * $requestedQuantity),
                    //     ]);
                    // }
                }
            }

            // Handle auto-complete if requested
            // if (isset($adminControls['auto_complete']) && $adminControls['auto_complete']) {
            //     $this->handleAutoComplete($orderReturn);
            // }

            return $orderReturn;
        });
    }

    /**
     * Handle auto-completion of return request
     */
    private function handleAutoComplete(OrderReturn $orderReturn): void
    {
        // Auto-approve all return products
        foreach ($orderReturn->returnProducts as $returnProduct) {
            $returnProduct->update([
                'quantity_approved' => $returnProduct->quantity_requested,
            ]);
        }

        // Update return status to approved
        $orderReturn->update([
            'status' => ReturnStatus::Approved,
            'approved_at' => now(),
        ]);

        // If should_restock is enabled, update inventory
        if ($orderReturn->should_restock) {
            $this->restockProducts($orderReturn);
        }

        // Complete the return
        $orderReturn->update([
            'status' => ReturnStatus::Completed,
            'completed_at' => now(),
        ]);
    }

    /**
     * Restock products back to inventory
     */
    private function restockProducts(OrderReturn $orderReturn): void
    {
        foreach ($orderReturn->returnProducts as $returnProduct) {
            $orderProduct = $returnProduct->orderProduct;
            $product = $orderProduct->product;

            // Update product stock
            if ($product && $returnProduct->quantity_approved > 0) {
                $product->increment('quantity', $returnProduct->quantity_approved);

                // Mark as restocked
                $returnProduct->update([
                    'is_restocked' => true,
                    'admin_notes' => ($returnProduct->admin_notes ?? '')."\nRestocked {$returnProduct->quantity_approved} units on ".now()->format('Y-m-d H:i:s'),
                ]);
            }
        }
    }

    /**
     * Check if products have already been restocked
     */
    private function isAlreadyRestocked(OrderReturn $orderReturn): bool
    {
        return $orderReturn->returnProducts()->where('is_restocked', true)->exists();
    }

    public function getReturnEligibilityStatus(Order $order): array
    {
        $status = [
            'eligible' => false,
            'reason' => '',
            'hours_remaining' => null,
        ];

        // Check if order is delivered
        if ($order->status !== \App\Helper\OrderStatus::Delivered) {
            $status['reason'] = 'Order must be delivered before it can be returned';

            return $status;
        }

        // Check if order has been delivered for at least 24 hours
        $hoursSinceDelivery = $order->updated_at->diffInHours(now());
        if ($hoursSinceDelivery < 24) {
            $status['reason'] = 'Returns are only available 24 hours after delivery';
            $status['hours_remaining'] = 24 - $hoursSinceDelivery;

            return $status;
        }

        // Check if all products are returnable
        $totalProducts = $order->products()->count();
        $returnableProducts = $order->products()->whereHas('product', function ($query) {
            $query->where('is_return', true);
        })->count();

        if ($returnableProducts === 0) {
            $status['reason'] = 'None of the products in this order are returnable';

            return $status;
        }

        if ($returnableProducts < $totalProducts) {
            $status['reason'] = 'Only complete orders with all returnable products can be returned';

            return $status;
        }

        // Check if there's already an active return
        if ($order->hasActiveReturn()) {
            $status['reason'] = 'This order already has an active return request';

            return $status;
        }

        $status['eligible'] = true;
        $status['reason'] = 'Order is eligible for return';

        return $status;
    }

    /**
     * Update admin controls for a return
     */
    public function updateAdminControls(OrderReturn $orderReturn, array $adminControls): bool
    {
        return DB::transaction(function () use ($orderReturn, $adminControls) {
            $updateData = [];

            // Update admin-controlled fields
            if (isset($adminControls['restocking_decision'])) {
                $updateData['restocking_decision'] = $adminControls['restocking_decision'];
            }

            if (isset($adminControls['should_restock'])) {
                $updateData['should_restock'] = $adminControls['should_restock'];
            }

            if (isset($adminControls['admin_notes'])) {
                $updateData['admin_notes'] = $adminControls['admin_notes'];
            }

            if (isset($adminControls['total_refund_amount'])) {
                $updateData['total_refund_amount'] = $adminControls['total_refund_amount'];
            }

            if (isset($adminControls['payment_slip'])) {
                $updateData['payment_slip'] = $adminControls['payment_slip'];
            }

            // If enabling should_restock and return is approved/processing, restock immediately
            if (isset($adminControls['should_restock']) &&
                $adminControls['should_restock'] &&
                ! $orderReturn->should_restock &&
                in_array($orderReturn->status, [ReturnStatus::Approved, ReturnStatus::Processing])) {

                $this->restockProducts($orderReturn);
            }

            return $orderReturn->update($updateData);
        });
    }

}
