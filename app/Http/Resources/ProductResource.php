<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            // 'brand' => new BrandResource($this->brand),
            // 'category' => new CategoryResource($this->category),

            // 'price' => $this->price,
            // 'discount' => $this->discount,
            // 'discount_price' => $this->discount_price,

            // 'exchange' => new ExchangeResource($this->exchange),

            // 'sku' => $this->sku,
            // 'quantity' => $this->quantity,

            // //TODO::odot
            // 'attributes' => $this->attributes,
            // 'variants' => $this->variants,

            // 'thumbnail' => $this->getFirstMediaUrl('images', 'thumb'),
            // 'images' => $this->getImages(),
        ];
    }

    private function getImages(): array
    {
        $imageList = [];
        foreach ($this->getMedia('images') as $item) {
            $imageList[] = $item->getUrl();
        }

        return $imageList;
    }

    private function getThumbnails(): array
    {
        $imageList = [];
        foreach ($this->getMedia('images') as $item) {
            $imageList[] = $item->getUrl('thumb');
        }

        return $imageList;
    }
}
