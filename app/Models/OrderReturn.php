<?php

namespace App\Models;

use App\Helper\MediaRegisterConversion;
use App\Helper\ReturnStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class OrderReturn extends Model implements HasMedia
{
    use BelongsToTenant, HasFactory, InteractsWithMedia, SoftDeletes;

    protected $table = 'order_returns';

    protected $fillable = [
        'invoice_number',
        'order_id',
        'seller_id',
        'tenant_id',
        'user_id',
        'status',
        'reason',
        'admin_notes',
        'total_refund_amount',
        'restocking_decision',
        'should_restock',
        'payment_slip',
        'requested_at',
        'approved_at',
        'completed_at',
    ];

    protected $casts = [
        'status' => ReturnStatus::class,
        'should_restock' => 'boolean',
        'total_refund_amount' => 'decimal:2',
        'requested_at' => 'datetime',
        'approved_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function registerMediaCollections(): void
    {
        (new MediaRegisterConversion('payment_slips'))->registerMediaConversions();
    }

    // Relationships
    public function order()
    {
        return $this->belongsTo(Order::class)->withTrashed();
    }

    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function returnProducts()
    {
        return $this->hasMany(OrderReturnProduct::class);
    }

    // Helper Methods
    public function canBeRequested(): bool
    {
        if (! $this->order) {
            return false;
        }

        // Check if order is delivered
        if ($this->order->status !== \App\Helper\OrderStatus::Delivered) {
            return false;
        }

        // Check if order has been delivered for at least 24 hours but not more than allowed window
        $deliveredAt = $this->order->updated_at; // Assuming updated_at reflects status change
        $now = Carbon::now();
        $hoursSinceDelivery = $deliveredAt->diffInHours($now);

        // Must be at least 24 hours since delivery
        if ($hoursSinceDelivery < 24) {
            return false;
        }

        // Check if all products are returnable
        return $this->order->products()->whereHas('product', function ($query) {
            $query->where('is_return', true);
        })->count() === $this->order->products()->count();
    }

    public function generateInvoiceNumber(): string
    {
        $prefix = 'RET';
        $date = now()->format('Ymd');
        $lastReturn = static::whereDate('created_at', today())
            ->latest('id')
            ->first();

        $sequence = $lastReturn ? (int) substr($lastReturn->invoice_number, -4) + 1 : 1;

        return sprintf('%s-%s-%04d', $prefix, $date, $sequence);
    }

    public function getTotalRefundableAmount(): float
    {
        if ($this->returnProducts->count() > 0) {
            // Calculate from approved return products
            return $this->returnProducts->sum('total_refund_amount');
        }

        // Fallback: calculate from all order products (for new returns)
        return $this->order->products->sum(function ($orderProduct) {
            $price = $orderProduct->discount_price > 0 ? $orderProduct->discount_price : $orderProduct->price;

            return $price * $orderProduct->quantity;
        });
    }

    public function approve(?string $adminNotes = null): bool
    {
        if ($this->status !== ReturnStatus::Pending && $this->status !== ReturnStatus::UnderReview) {
            return false;
        }

        $this->update([
            'status' => ReturnStatus::Approved,
            'approved_at' => now(),
            'admin_notes' => $adminNotes,
            'total_refund_amount' => $this->getTotalRefundableAmount(),
        ]);

        return true;
    }

    public function reject(string $reason): bool
    {
        if ($this->status !== ReturnStatus::Pending && $this->status !== ReturnStatus::UnderReview) {
            return false;
        }

        $this->update([
            'status' => ReturnStatus::Rejected,
            'admin_notes' => $reason,
        ]);

        return true;
    }

    public function complete(): bool
    {
        if ($this->status !== ReturnStatus::Processing) {
            return false;
        }

        // Update order products based on approved return products
        foreach ($this->returnProducts as $returnProduct) {
            if ($returnProduct->quantity_approved > 0) {
                $orderProduct = $returnProduct->orderProduct;

                // Calculate new returned quantities
                $newReturnedQuantity = $orderProduct->returned_quantity + $returnProduct->quantity_approved;
                $newRefundedAmount = $orderProduct->total_refunded_amount + $returnProduct->total_refund_amount;

                // Update order product
                $orderProduct->update([
                    'is_returned' => true,
                    'returned_quantity' => $newReturnedQuantity,
                    'total_refunded_amount' => $newRefundedAmount,
                ]);
            }
        }

        // Handle restocking if approved
        if ($this->should_restock && $this->restocking_decision === 'restock') {
            $this->restockProducts();
        }

        $this->update([
            'status' => ReturnStatus::Completed,
            'completed_at' => now(),
        ]);

        return true;
    }

    protected function restockProducts(): void
    {
        foreach ($this->returnProducts as $returnProduct) {
            if ($returnProduct->quantity_approved > 0 && $returnProduct->canBeRestocked()) {
                $returnProduct->restockProduct();
            }
        }
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', ReturnStatus::Pending);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', ReturnStatus::Approved);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', ReturnStatus::Completed);
    }

    public function scopeForSeller($query, $sellerId)
    {
        return $query->where('seller_id', $sellerId);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
