<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Category extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    //    protected $fillable = [
    //        'name','parent_id','seller_id','description'
    //    ];

    public function subCategories()
    {
        return $this->hasMany(Category::class, 'parent_id', 'id')->withTrashed();
    }

    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
