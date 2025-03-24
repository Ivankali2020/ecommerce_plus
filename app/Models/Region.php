<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Region extends Model
{
    use HasFactory,SoftDeletes;

    public function townships()
    {
        return $this->hasMany(Township::class)->withTrashed();
    }

    public function orders()
    {
        return $this->hasManyThrough(
            Order::class,
            UserAddress::class,
            'region_id',    // Foreign key on UserAddress table
            'user_address_id', // Foreign key on Order table
            'id',           // Local key on Region table
            'id'            // Local key on UserAddress table
        );
    }
}
