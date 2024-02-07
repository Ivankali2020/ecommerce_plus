<?php

namespace App\Services;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Seller;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class ProductService
{
    /**
     * Get Brands
     *
     * @param int $sellerId
     * @return Collection $brands
     */
    public function getBrands($sellerId): Collection
    {
        $seller = Seller::findOrFail($sellerId);
        $categories = $seller->brands;
        return $categories;
    }

    /**
     * Get Brands
     *
     * @param int $sellerId
     * @return Collection $categories
     */
    public function getCategories($sellerId): Collection
    {
        $seller = Seller::findOrFail($sellerId);
        $categories = $seller->categories->where('parent_id', null);
        return $categories;
    }

    /**
     * Get Products
     *
     * @param int $sellerId
     * @param Request $parms
     * @return LengthAwarePaginator $products
     */
    public function getProducts($parms, $sellerId): LengthAwarePaginator
    {
        $seller = Seller::findOrFail($sellerId);
        $query = Product::query();

        $query->where('seller_id', $seller->id);

        //TODO::By Topic, Search and filters

        $products = $query->paginate(20);

        return $products;
    }
}
