<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class ExpanseCategory extends Model
{
    use BelongsToTenant;

    public function expanses()
    {
        return $this->hasMany(Expanse::class, 'expanse_category_id');
    }
}
