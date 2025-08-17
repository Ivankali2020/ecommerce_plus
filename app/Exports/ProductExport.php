<?php

namespace App\Exports;

use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProductExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Product::with(['category', 'brand', 'supplier', 'exchange.currency', 'variants'])->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'SKU',
            'Category',
            'Brand',
            'Supplier',
            'Currency',
            'Buy Price',
            'Price',
            'MMK Buy Price',
            'MMK Price',
            'Quantity',
            'Discount',
            'Discount Price',
            'MOQ',
            'Has Variant',
            'Is Public',
            'Is Recommended',
            'Is Wholesale',
            'Is Return',
            'Is Shipped',
            'Attributes',
            'Variants Count',
            'Created At',
        ];
    }

    public function map($product): array
    {
        // Convert attributes JSON to readable format
        $attributesString = '';
        if ($product->attributes) {

            $attributeData = $product->attributes;
            $attrs = [];
            foreach ($attributeData as $item) {

                if (isset($item['attribute_id']) && isset($item['attribute_value_id'])) {
                    $attribute = Attribute::find($item['attribute_id']);
                    if ($attribute) {
                        $values = AttributeValue::whereIn('id', $item['attribute_value_id'])
                            ->pluck('value')
                            ->toArray();
                        $attrs[] = $attribute->name.':'.implode(',', $values);
                    }
                }
            }
            $attributesString = implode(';', $attrs);
        }

        return [
            $product->id,
            $product->name,
            $product->sku,
            $product->category?->name,
            $product->brand?->name,
            $product->supplier?->name,
            $product->exchange?->currency?->code,
            $product->buy_price,
            $product->price,
            $product->mmk_buy_price,
            $product->mmk_price,
            $product->quantity,
            $product->discount,
            $product->discount_price,
            $product->MOQ,
            $product->has_variant ? 'Yes' : 'No',
            $product->is_public ? 'Yes' : 'No',
            $product->is_recommended ? 'Yes' : 'No',
            $product->is_whole_sale ? 'Yes' : 'No',
            $product->is_return ? 'Yes' : 'No',
            $product->is_shipped ? 'Yes' : 'No',
            $attributesString,
            $product->variants->count(),
            $product->created_at == null ? '' : $product->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
