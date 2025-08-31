<?php

namespace App\Models;

use App\Observers\OrderReturnProductObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToPrimaryModel;

class OrderReturnProduct extends Model
{
    use BelongsToPrimaryModel, HasFactory;

    protected $fillable = [
        'order_return_id',
        'order_product_id',
        'quantity_requested',
        'quantity_approved',
        'individual_reason',
        'condition_notes',
        'unit_refund_amount',
        'total_refund_amount',
        'is_restocked',
        'admin_notes',
    ];

    protected $casts = [
        'quantity_requested' => 'integer',
        'quantity_approved' => 'integer',
        'unit_refund_amount' => 'decimal:2',
        'total_refund_amount' => 'decimal:2',
        'is_restocked' => 'boolean',
    ];

    public function getRelationshipToPrimaryModel(): string
    {
        return 'orderReturn.order';
    }

    // Relationships
    public function orderReturn()
    {
        return $this->belongsTo(OrderReturn::class);
    }

    public function orderProduct()
    {
        return $this->belongsTo(OrderProduct::class);
    }

    // Helper Methods
    public function calculateRefundAmount(): void
    {
        if (! $this->orderProduct) {
            return;
        }

        // Use the discounted price if available, otherwise use regular price
        $unitPrice = $this->orderProduct->discount_price > 0
            ? $this->orderProduct->discount_price
            : $this->orderProduct->price;

        $this->unit_refund_amount = $unitPrice;
        $this->total_refund_amount = $unitPrice * $this->quantity_approved;
        $this->save();
    }

    public function approve(int $approvedQuantity, ?string $adminNotes = null): bool
    {
        // Validate approved quantity doesn't exceed requested quantity
        if ($approvedQuantity > $this->quantity_requested) {
            return false;
        }

        // Validate approved quantity doesn't exceed available quantity from original order
        if ($approvedQuantity > $this->orderProduct->quantity) {
            return false;
        }

        $this->update([
            'quantity_approved' => $approvedQuantity,
            'admin_notes' => $adminNotes,
        ]);

        $this->calculateRefundAmount();

        return true;
    }

    public function restockProduct(): bool
    {
        if (! $this->quantity_approved || $this->is_restocked) {
            return false;
        }

        $orderProduct = $this->orderProduct;

        if ($orderProduct->variant_id) {
            // Restock variant
            $orderProduct->variant()->increment('quantity', $this->quantity_approved);
        } else {
            // Restock main product
            $orderProduct->product()->increment('quantity', $this->quantity_approved);
        }

        $this->update(['is_restocked' => true]);

        return true;
    }

    public function canBeRestocked(): bool
    {
        return $this->quantity_approved > 0 && ! $this->is_restocked;
    }

    // Scopes
    public function scopeApproved($query)
    {
        return $query->where('quantity_approved', '>', 0);
    }

    public function scopeRestocked($query)
    {
        return $query->where('is_restocked', true);
    }

    public function scopeNotRestocked($query)
    {
        return $query->where('is_restocked', false);
    }

    // protected static function booted()
    // {
    //     static::observe(OrderReturnProductObserver::class);
    // }
}
