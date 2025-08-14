<?php

namespace App\Imports;

use App\Helper\AttributeType;
use App\Helper\CheckFilamentSellerUser;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Supplier;
use App\Models\Exchange;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Seller;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ProductImport implements ToCollection, WithHeadingRow, WithValidation, WithBatchInserts
{
    public function collection(Collection $collection)
    {
        $sellerId = CheckFilamentSellerUser::getSellerId();
        $tenantId = tenant('id');
        foreach ($collection as $row) {
            // Skip empty rows
            if (empty($row['name'])) {
                continue;
            }

            // Find or create related models
            $category = Category::firstOrCreate(['name' => $row['category'], 'seller_id' => $sellerId]);
            $brand = !empty($row['brand']) ? Brand::firstOrCreate(['name' => $row['brand'], 'seller_id' => $sellerId]) : null;
            $supplier = !empty($row['supplier']) ? Supplier::where('name', $row['supplier'])->first() : null;
            $exchange = Exchange::whereHas('currency', function ($q) use ($row) {
                $q->where('code', $row['currency']);
            })->first();

            if (!$category || !$exchange) {
                continue; // Skip if required relationships not found
            }

            // Process attributes if provided
            $attributesData = null;
            if (!empty($row['attributes'])) {
                $attributesData = $this->processAttributes($row['attributes'], $sellerId);
            }

            Product::create([
                'seller_id' => $sellerId, // Adjust based on your auth logic
                'tenant_id' => $tenantId,
                'name' => $row['name'],
                'sku' => $row['sku'] ?? uniqid(),
                'description' => $row['description'] ?? '',
                'category_id' => $category->id,
                'brand_id' => $brand?->id,
                'supplier_id' => $supplier?->id,
                'exchange_id' => $exchange->id,
                'attributes' => $attributesData,
                'buy_price' => $row['buy_price'] ?? 0,
                'price' => $row['price'] ?? 0,
                'quantity' => $row['quantity'] ?? 0,
                'discount' => $row['discount'] ?? 0,
                'discount_price' => $row['discount_price'] ?? 0,
                'MOQ' => $row['moq'] ?? 1,
                'has_variant' => $this->parseBooleanValue($row['has_variant'] ?? 'No'),
                'is_public' => $this->parseBooleanValue($row['is_public'] ?? 'Yes'),
                'is_recommended' => $this->parseBooleanValue($row['is_recommended'] ?? 'No'),
                'is_whole_sale' => $this->parseBooleanValue($row['is_wholesale'] ?? 'No'),
                'is_return' => $this->parseBooleanValue($row['is_return'] ?? 'No'),
                'is_shipped' => $this->parseBooleanValue($row['is_shipped'] ?? 'No'),
            ]);
        }
    }

    private function parseBooleanValue($value): bool
    {
        return strtolower($value) === 'yes' || $value === '1' || $value === 1 || $value === true;
    }

    public function batchSize(): int
    {
        return 1000;
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
                ['tenant_id' => tenant('id'), 'type' => AttributeType::Text,]
            );

            // Split values by comma
            $values = array_map('trim', explode(',', $valuesString));
            $valueIds = [];

            foreach ($values as $valueName) {
                if (empty($valueName)) continue;

                // Find or create attribute value
                $attributeValue = AttributeValue::firstOrCreate([
                    'attribute_id' => $attribute->id,
                    'value' => $valueName,
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
            'name' => 'required|string|max:255',
            'category' => 'required|string',
            'currency' => 'nullable|string',
            'price' => 'nullable|numeric',
            'quantity' => 'nullable|integer',
            'buy_price' => 'nullable|numeric',
            'discount' => 'nullable|numeric',
            'discount_price' => 'nullable|numeric',
            'moq' => 'nullable|integer',
            'has_variant' => 'nullable|string|in:Yes,No',
            'is_public' => 'nullable|string|in:Yes,No',
            'is_recommended' => 'nullable|string|in:Yes,No',
            'is_wholesale' => 'nullable|string|in:Yes,No',
            'is_return' => 'nullable|string|in:Yes,No',
            'is_shipped' => 'nullable|string|in:Yes,No',
            'sku' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'brand' => 'nullable|string',
            'supplier' => 'nullable|string',
            'attributes' => 'nullable|string', // Format: "size:lg,xl;color:red,blue"
        ];
    }
}
