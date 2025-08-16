<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToPrimaryModel;

class Review extends Model
{
    use BelongsToPrimaryModel, HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'stars',
        'comment',
    ];

    protected $casts = [
        'stars' => 'integer',
    ];

    public function getRelationshipToPrimaryModel(): string
    {
        return 'product';
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
