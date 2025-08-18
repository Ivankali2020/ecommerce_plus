<?php

namespace App\Exports;

use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Variant;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class VariantExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Variant::with(['product.category', 'product.brand', 'product.supplier', 'product.exchange.currency'])->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Product ID',
            'Product Name',
            'Variant Name',
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
            'Is Wholesale',
            'Attributes',
            'Created At',
        ];
    }

    public function map($variant): array
    {
        // Convert attributes JSON to readable format
        $attributesString = '';
        if ($variant->attributes) {
            $attributeData = $variant->attributes;
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
            $variant->id,
            $variant->product_id,
            $variant->product?->name,
            $variant->name,
            $variant->sku,
            $variant->product?->category?->name,
            $variant->product?->brand?->name,
            $variant->product?->supplier?->name,
            $variant->product?->exchange?->currency?->code,
            $variant->buy_price,
            $variant->price,
            $variant->mmk_buy_price,
            $variant->mmk_price,
            $variant->quantity,
            $variant->discount,
            $variant->discount_price,
            $variant->MOQ,
            $variant->is_whole_sale ? 'Yes' : 'No',
            $attributesString,
            $variant->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
