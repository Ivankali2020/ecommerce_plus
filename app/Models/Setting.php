<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Setting extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'seller_id',
        'tenant_id',
        'data',
    ];
    
    protected $casts = [
        'data' => 'array',
    ];
}
