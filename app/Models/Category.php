<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use function Symfony\Component\Translation\t;

class Category extends Model
{
    use HasFactory,SoftDeletes;

//    protected $fillable = [
//        'name','parent_id','seller_id','description'
//    ];

    public function subCategories()
    {
        return $this->hasMany(Category::class,'parent_id','id')->withTrashed();
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
