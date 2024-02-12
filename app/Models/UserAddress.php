<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserAddress extends Model
{
    use HasFactory,SoftDeletes;

    protected $appends = ['modAddress'];

    public function region()
    {
        return $this->belongsTo(Region::class)->withTrashed();
    }

    public function township()
    {
        return $this->belongsTo(Township::class)->withTrashed();
    }

    public function getModAddressAttribute()
    {
        return $this->region->mm_name .','. $this->township->mm_name .','.$this->address;
    }
}
