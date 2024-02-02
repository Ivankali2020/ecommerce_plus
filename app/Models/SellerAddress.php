<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SellerAddress extends Model
{
    use HasFactory,SoftDeletes;

    public function region()
    {
        return $this->belongsTo(Region::class)->withTrashed();
    }

    public function township()
    {
        return $this->belongsTo(Township::class)->withTrashed();
    }
}
