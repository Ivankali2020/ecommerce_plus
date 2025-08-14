<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToPrimaryModel;

class Wishlist extends Model
{
    use HasFactory,BelongsToPrimaryModel;

    public function getRelationshipToPrimaryModel(): string
    {
        return 'product';
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
