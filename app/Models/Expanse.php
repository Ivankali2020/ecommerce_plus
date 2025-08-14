<?php

namespace App\Models;

use App\Helper\PaymentMethodExpanse;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Expanse extends Model
{
    use BelongsToTenant;
    
    protected $cast = [
        'payment_method' => PaymentMethodExpanse::class,
    ];

    protected $fillable = ['expanse_category_id', 'amount', 'description', 'payment_method', 'seller_id'];

    public function category()
    {
        return $this->belongsTo(ExpanseCategory::class, 'expanse_category_id');
    }

    public function seller()
    {
        return $this->belongsTo(Seller::class, 'seller_id');
    }
}
