<?php

namespace App\Models;

use App\Helper\AttributeType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Attribute extends Model
{
    use HasFactory,SoftDeletes, BelongsToTenant;

    protected $casts = [
        'type' => AttributeType::class,
    ];

    public function values()
    {
        return $this->hasMany(AttributeValue::class)->withTrashed();
    }

    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }
}
