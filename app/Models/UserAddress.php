<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Database\Concerns\BelongsToPrimaryModel;

class UserAddress extends Model
{
    use HasFactory,SoftDeletes,BelongsToPrimaryModel;
    
    protected $appends = ['modAddress'];

    public function getRelationshipToPrimaryModel(): string
    {
        return 'user';
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

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
        return $this->region->mm_name.','.$this->township->mm_name.','.$this->address;
    }
}
