<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Database\Concerns\BelongsToPrimaryModel;

class AttributeValue extends Model
{
    use HasFactory,SoftDeletes, BelongsToPrimaryModel;


    public function getRelationshipToPrimaryModel(): string
    {
        return 'attribute';
    }

    public function attribute()
    {
        return $this->belongsTo(Attribute::class)->withTrashed();
    }
}
