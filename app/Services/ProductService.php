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
    public function getProducts($params, $sellerId): LengthAwarePaginator
    {
        $seller = Seller::findOrFail($sellerId);

        $query = Product::where('seller_id', $seller->id)
            ->when(isset($params['brand_id']), function ($q) use ($params) {
                $q->where('brand_id', $params['brand_id']);
            })
            ->when(isset($params['category_id']), function ($q) use ($params) {
                $q->where('category_id', $params['category_id']);
            })
            ->when(isset($params['min_price']), function ($q) use ($params) {
                $q->where('price', '>=', $params['min_price']);
            })
            ->when(isset($params['max_price']), function ($q) use ($params) {
                $q->where('price', '<=', $params['max_price']);
            })
            ->when(isset($params['q']), function ($q) use ($params) {
                $q->where(function ($query) use ($params) {
                    $query->where('name', 'like', '%' . $params['q'] . '%')
                        ->orWhere('description', 'like', '%' . $params['q'] . '%');
                });
            });

        $perPage = $params['limit'] ?? 20;
        $products = $query->paginate($perPage);

        return $products;
    }
}
