<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\BrandResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ProductResource;
use App\Models\Seller;
use App\Services\ProductService;
use App\Traits\ResponseTraits;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    use ResponseTraits;

    /**
     * @var UserAuthService $userAuthService
     */
    private ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }


    public function getBrands($sellerId)
    {
        $brands = $this->productService->getBrands($sellerId);
        return $this->success('Get brands successful', BrandResource::collection($brands));
    }

    public function getCategories($sellerId)
    {
        $categories = $this->productService->getCategories($sellerId);
        return $this->success('Get categories successful', CategoryResource::collection($categories));
    }

    public function getProducts(Request $request, $sellerId)
    {
        $products = $this->productService->getProducts($request, $sellerId);
        // return $products;
        $data = [
            'posts' => ProductResource::collection($products),
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'total' => $products->total(),
                'per_page' => $products->perPage(),
            ],
            'links' => [
                'next' => $products->nextPageUrl(),
                'prev' => $products->previousPageUrl(),
            ],
        ];
        return $this->success('Get products successful', $data);
    }
}
