<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CurrencyResource extends JsonResource
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
            'code' => $this->code,
            'symbol' => $this->symbol,
            'symbol_native' => $this->symbol_native,
            'name' => $this->name,
            'name_plural' => $this->name_plural,
        ];
    }
}
