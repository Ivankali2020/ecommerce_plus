<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Database\Concerns\BelongsToPrimaryModel;

class PaymentAccount extends Model
{
    use HasFactory,SoftDeletes,BelongsToPrimaryModel;

    public function getRelationshipToPrimaryModel(): string
    {
        return 'payment';
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}
