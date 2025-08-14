<?php

namespace App\Imports;

use App\Helper\AttributeType;
use App\Models\Variant;
use App\Models\Product;
use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class VariantImport implements ToCollection, WithHeadingRow, WithValidation
{
    public function collection(Collection $collection)
    {
        foreach ($collection as $row) {
            // Skip empty rows
            if (empty($row['variant_name']) || empty($row['product_id'])) {
                continue;
            }

            // Find the product
            $product = Product::find($row['product_id']);
            if (!$product) {
                continue; // Skip if product not found
            }

            // Parse attributes if provided
            $attributes = null;
            if (!empty($row['attributes'])) {
                $attributes = $this->processAttributes($row['attributes'], $product->seller_id);
            }

            Variant::create([
                'product_id' => $product->id,
                'name' => $row['variant_name'],
                'sku' => $row['sku'] ?? uniqid(),
                'attributes' => $attributes,
                'buy_price' => $row['buy_price'] ?? 0,
                'price' => $row['price'] ?? 0,
                'quantity' => $row['quantity'] ?? 0,
                'discount' => $row['discount'] ?? 0,
                'discount_price' => $row['discount_price'] ?? 0,
                'MOQ' => $row['moq'] ?? 1,
                'is_whole_sale' => $this->parseBooleanValue($row['is_wholesale'] ?? 'No'),
            ]);
        }
    }

    private function parseBooleanValue($value): bool
    {
        return strtolower($value) === 'yes' || $value === '1' || $value === 1 || $value === true;
    }

    /**
     * Process attributes string and return formatted JSON data
     * Expected format: "size:lg,xl;color:red,blue"
     * @param string $attributesString
     * @param int $sellerId
     * @return array|null
     */
    private function processAttributes(string $attributesString, int $sellerId): ?array
    {
        if (empty($attributesString)) {
            return null;
        }

        $formattedAttributes = [];

        // Split by semicolon to get individual attributes
        $attributePairs = explode(';', trim($attributesString));

        foreach ($attributePairs as $pair) {
            $pair = trim($pair);
            if (empty($pair)) continue;

            // Split by colon to separate attribute name from values
            $parts = explode(':', $pair, 2);
            if (count($parts) !== 2) continue;

            $attributeName = trim($parts[0]);
            $valuesString = trim($parts[1]);

            if (empty($attributeName) || empty($valuesString)) continue;

            // Find or create attribute
            $attribute = Attribute::firstOrCreate(
                ['name' => $attributeName, 'seller_id' => $sellerId],
                ['tenant_id' => tenant('id'), 'type' => AttributeType::Text]
            );

            // Split values by comma
            $values = array_map('trim', explode(',', $valuesString));
            $valueIds = [];

            foreach ($values as $valueName) {
                if (empty($valueName)) continue;

                // Find or create attribute value
                $attributeValue = AttributeValue::firstOrCreate([
                    'attribute_id' => $attribute->id,
                    'value' => $valueName
                ]);

                $valueIds[] = $attributeValue->id;
            }

            if (!empty($valueIds)) {
                $formattedAttributes[] = [
                    'attribute_id' => $attribute->id,
                    'attribute_value_id' => $valueIds
                ];
            }
        }

        return empty($formattedAttributes) ? null : $formattedAttributes;
    }

    public function rules(): array
    {
        return [
            'product_id' => 'required|integer|exists:products,id',
            'variant_name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'quantity' => 'required|integer',
            'buy_price' => 'nullable|numeric',
            'discount' => 'nullable|numeric',
            'discount_price' => 'nullable|numeric',
            'moq' => 'nullable|integer',
            'is_wholesale' => 'nullable|string|in:Yes,No',
            'sku' => 'nullable|string|max:255',
            'attributes' => 'nullable|string', // Format: "size:lg,xl;color:red,blue"
        ];
    }
}
