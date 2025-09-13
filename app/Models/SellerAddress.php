<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class SellerAddress extends Model
{
    use HasFactory,SoftDeletes,BelongsToTenant;

    protected $appends = ['modAddress'];

    public function region()
    {
        return $this->belongsTo(Region::class)->withTrashed();
    }

    public function township()
    {
        return $this->belongsTo(Township::class)->withTrashed();
    }

    public function getModAddressAttribute() : string
    {
        return $this->address . ', ' . ($this->township ? $this->township->name . ', ' : '') . ($this->region ? $this->region->name : '');
    }
}
