<?php

namespace App\Models;

use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Database\Models\Domain;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains;

    protected $fillable = [
        'data',
    ];

    public function domain()
    {
        return $this->hasOne(Domain::class, 'tenant_id', 'id');
    }

    public function sellers()
    {
        return $this->belongsToMany(Seller::class, 'tenant_seller', 'tenant_id', 'seller_id');
    }
}
