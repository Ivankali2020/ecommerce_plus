<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Delivery extends Model
{
    use HasFactory,SoftDeletes,BelongsToTenant;

    public function region()
    {
        return $this->belongsTo(Region::class)->withTrashed();
    }

    public function township()
    {
        return $this->belongsTo(Township::class)->withTrashed();
    }

    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }
}
