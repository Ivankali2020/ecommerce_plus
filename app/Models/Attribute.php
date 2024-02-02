<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use function Symfony\Component\Translation\t;

class Attribute extends Model
{
    use HasFactory,SoftDeletes;

    public function values()
    {
        return $this->hasMany(AttributeValue::class)->withTrashed();
    }

    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }
}
