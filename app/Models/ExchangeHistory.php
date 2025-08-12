<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToPrimaryModel;

class ExchangeHistory extends Model
{
    use HasFactory, BelongsToPrimaryModel;

    public function getRelationshipToPrimaryModel(): string
    {
        return 'exchange';
    }

    public function exchange()
    {
        return $this->belongsTo(Exchange::class);
    }
}
