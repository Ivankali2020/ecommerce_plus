<?php

namespace Database\Seeders;

use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Currency;
use App\Models\Exchange;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Payment;
use App\Models\PaymentAccount;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseProduct;
use App\Models\Seller;
use App\Models\Tenant;
use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stancl\Tenancy\Database\Models\Domain;

class MyintDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Starting Myint data import...');

        DB::beginTransaction();

        try {

             // create tenant
            $tenant = Tenant::create([
                'data' => [
                    'domain' => 'ab',
                ],
            ]);

            $user = Seller::factory(1)
                ->state([
                    'email' => 'ab@gmail.com',
                    'name' => 'AP Pharmacy',
                    'phone' => '09 454949610',
                    'description' => 'ဆေးဆိုင်',
                    'email_verified_at' => now(),
                    'tenant_id' => $tenant->id,
                ])
                // ->has(Attribute::factory(3)->has(AttributeValue::factory(3), 'values'))
                ->has(Payment::factory(5)->state(fn () => ['tenant_id' => isset($tenant) ? $tenant->id : null])->has(PaymentAccount::factory(2), 'accounts'))
                ->has(User::factory(3)->state(fn () => ['tenant_id' => isset($tenant) ? $tenant->id : null])->has(UserAddress::factory(2), 'addresses'))
                ->create()->toArray()[0];

            // create domains
            $centralDomainIndex = config('app.env') === 'local' ? 1 : 0;
            $domain = Domain::create([
                'domain' => 'ab'.'.'.config('tenancy.central_domains')[$centralDomainIndex],
                'tenant_id' => $tenant->id,
            ]);

            // attach tenant to seller
            DB::table('tenant_seller')->insert([
                'tenant_id' => $tenant->id,
                'seller_id' => $user['id'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $currency = Currency::where('code', 'MMK')->first();

            $exchange = new Exchange();
            $exchange->currency_id = $currency->id;
            $exchange->rate = '4500';
            $exchange->seller_id = $user['id'];
            $exchange->tenant_id = $tenant->id;
            $exchange->save();
            // Import in order of dependencies
            $this->importAttributes();
            $this->importAttributeValues();
            $this->importBrands();
            $this->importCategories();
            $this->importProducts();
            $this->importOrders();
            $this->importOrderProducts();
            // $this->importPurchases();
            // $this->importPurchaseProducts();

            DB::commit();
            $this->command->info('Myint data import completed successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Import failed: ' . $e->getMessage());
            Log::error('Myint data import failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    private function importAttributes(): void
    {
        $this->command->info('Importing attributes...');
        
        $attributesData = json_decode(file_get_contents(public_path('myint/attributes.json')), true);
        $imported = 0;
        $skipped = 0;

        foreach ($attributesData as $attributeData) {
            // Skip records with deleted_at not null
            if (!is_null($attributeData['deleted_at'])) {
                $skipped++;
                continue;
            }

            try {
                Attribute::create([
                    'id' => $attributeData['id'],
                    'seller_id' => 1,
                    'tenant_id' => 1, // Default tenant_id
                    'name' => $attributeData['name'],
                    'type' => $attributeData['type'],
                    'created_at' => $attributeData['created_at'],
                    'updated_at' => $attributeData['updated_at'],
                ]);
                $imported++;
            } catch (\Exception $e) {
                $this->command->warn("Failed to import attribute ID {$attributeData['id']}: " . $e->getMessage());
            }
        }

        $this->command->info("Attributes: {$imported} imported, {$skipped} skipped");
    }

    private function importAttributeValues(): void
    {
        $this->command->info('Importing attribute values...');
        
        // Get list of imported attribute IDs to check foreign key constraints
        $importedAttributeIds = Attribute::pluck('id')->toArray();
        
        $attributeValuesData = json_decode(file_get_contents(public_path('myint/attribute_values.json')), true);
        $imported = 0;
        $skipped = 0;

        foreach ($attributeValuesData as $attributeValueData) {
            // Skip records with deleted_at not null, incomplete data, or invalid attribute_id reference
            if (!is_null($attributeValueData['deleted_at'] ?? null) || 
                !isset($attributeValueData['id']) || 
                !isset($attributeValueData['attribute_id']) ||
                !in_array($attributeValueData['attribute_id'], $importedAttributeIds)) {
                $skipped++;
                continue;
            }

            try {
                AttributeValue::create([
                    'id' => $attributeValueData['id'],
                    'attribute_id' => $attributeValueData['attribute_id'],
                    'value' => $attributeValueData['value'],
                    'color_code' => $attributeValueData['color_code'],
                    'created_at' => $attributeValueData['created_at'] ?? now(),
                    'updated_at' => $attributeValueData['updated_at'] ?? now(),
                ]);
                $imported++;
            } catch (\Exception $e) {
                $this->command->warn("Failed to import attribute value ID {$attributeValueData['id']}: " . $e->getMessage());
            }
        }

        $this->command->info("Attribute values: {$imported} imported, {$skipped} skipped");
    }

    private function importBrands(): void
    {
        $this->command->info('Importing brands...');
        
        $brandsData = json_decode(file_get_contents(public_path('myint/brands.json')), true);
        $imported = 0;
        $skipped = 0;

        foreach ($brandsData as $brandData) {
            // Skip records with deleted_at not null
            if (!is_null($brandData['deleted_at']) || $brandData['seller_id'] == 1) {
                $skipped++;
                continue;
            }

            try {
                Brand::create([
                    'id' => $brandData['id'],
                    'seller_id' => 1,
                    'tenant_id' => 1, // Default tenant_id
                    'name' => $brandData['name'],
                    'description' => $brandData['description'],
                    'created_at' => $brandData['created_at'],
                    'updated_at' => $brandData['updated_at'],
                ]);
                $imported++;
            } catch (\Exception $e) {
                $this->command->warn("Failed to import brand ID {$brandData['id']}: " . $e->getMessage());
            }
        }

        $this->command->info("Brands: {$imported} imported, {$skipped} skipped");
    }

    private function importCategories(): void
    {
        $this->command->info('Importing categories...');
        
        $categoriesData = json_decode(file_get_contents(public_path('myint/categories.json')), true);
        $imported = 0;
        $skipped = 0;

        foreach ($categoriesData as $categoryData) {
            // Skip records with deleted_at not null
            if (!is_null($categoryData['deleted_at']) || $categoryData['seller_id'] == 1) {
                $skipped++;
                continue;
            }

            try {
                Category::create([
                    'id' => $categoryData['id'],
                    'seller_id' => 1,
                    'tenant_id' => 1, // Default tenant_id
                    'parent_id' => $categoryData['parent_id'],
                    'description' => $categoryData['description'],
                    'name' => $categoryData['name'],
                    'is_sub_category' => (bool) $categoryData['is_sub_category'],
                    'created_at' => $categoryData['created_at'],
                    'updated_at' => $categoryData['updated_at'],
                ]);
                $imported++;
            } catch (\Exception $e) {
                $this->command->warn("Failed to import category ID {$categoryData['id']}: " . $e->getMessage());
            }
        }

        $this->command->info("Categories: {$imported} imported, {$skipped} skipped");
    }

    private function importProducts(): void
    {
        $this->command->info('Importing products...');
        
        // Get list of imported IDs to check foreign key constraints
        $importedBrandIds = Brand::pluck('id')->toArray();
        $importedCategoryIds = Category::pluck('id')->toArray();
        
        $productsData = json_decode(file_get_contents(public_path('myint/products.json')), true);
        $imported = 0;
        $skipped = 0;

        foreach ($productsData as $productData) {
            // Skip records with deleted_at not null or invalid foreign key references
            if (!is_null($productData['deleted_at']) ||
                $productData['seller_id'] == 1 ||
                ($productData['brand_id'] && !in_array($productData['brand_id'], $importedBrandIds)) ||
                !in_array($productData['category_id'], $importedCategoryIds)) {
                $skipped++;
                continue;
            }

            try {
                Product::create([
                    'id' => $productData['id'],
                    'seller_id' => 1,
                    'tenant_id' => 1, // Default tenant_id
                    'supplier_id' => $productData['supplier_id'],
                    'brand_id' => $productData['brand_id'],
                    'category_id' => $productData['category_id'],
                    'exchange_id' => 1, // Default exchange_id
                    'name' => $productData['name'],
                    'description' => $productData['description'],
                    'attributes' => $productData['attributes'] ? json_decode($productData['attributes'], true) : null,
                    'MOQ' => $productData['MOQ'],
                    'sku' => $productData['sku'],
                    'buy_price' => $productData['buy_price'],
                    'price' => $productData['price'],
                    'quantity' => $productData['quantity'],
                    'discount' => $productData['discount'],
                    'discount_price' => $productData['discount_price'],
                    'mmk_buy_price' => $productData['mmk_buy_price'],
                    'mmk_price' => $productData['mmk_price'],
                    'mmk_discount_price' => $productData['mmk_discount_price'],
                    'has_variant' => (bool) $productData['has_variant'],
                    'is_public' => (bool) $productData['is_public'],
                    'is_recommended' => (bool) $productData['is_recommended'],
                    'is_whole_sale' => (bool) $productData['is_whole_sale'],
                    'is_return' => (bool) $productData['is_return'],
                    'is_shipped' => (bool) $productData['is_shipped'],
                    'expired_date' => $productData['expired_date'] ?? null,
                    'created_at' => $productData['created_at'] ?? now(),
                    'updated_at' => $productData['updated_at'] ?? now(),
                ]);
                $imported++;
            } catch (\Exception $e) {
                $this->command->warn("Failed to import product ID {$productData['id']}: " . $e->getMessage());
            }
        }

        $this->command->info("Products: {$imported} imported, {$skipped} skipped");
    }

    private function importPurchases(): void
    {
        $this->command->info('Importing purchases...');
        
        $purchasesData = json_decode(file_get_contents(public_path('myint/purchases.json')), true);
        $imported = 0;

        foreach ($purchasesData as $purchaseData) {
            try {
                Purchase::create([
                    'id' => $purchaseData['id'],
                    'seller_id' => 1,
                    'tenant_id' => 1, // Default tenant_id
                    'supplier_id' => $purchaseData['supplier_id'],
                    'status' => $purchaseData['status'],
                    'total_amount' => $purchaseData['total_amount'],
                    'paid_amount' => $purchaseData['paid_amount'],
                    'remaining_amount' => $purchaseData['remaining_amount'],
                    'created_at' => $purchaseData['created_at'],
                    'updated_at' => $purchaseData['updated_at'],
                ]);
                $imported++;
            } catch (\Exception $e) {
                $this->command->warn("Failed to import purchase ID {$purchaseData['id']}: " . $e->getMessage());
            }
        }

        $this->command->info("Purchases: {$imported} imported");
    }

    private function importPurchaseProducts(): void
    {
        $this->command->info('Importing purchase products...');
        
        $purchaseProductsData = json_decode(file_get_contents(public_path('myint/purchase_products.json')), true);
        $imported = 0;

        $purchaseProductsDataIDs = Purchase::pluck('id')->toArray();

        foreach ($purchaseProductsData as $purchaseProductData) {
            try {
                if (!in_array($purchaseProductData['purchase_id'], $purchaseProductsDataIDs)) {
                    $this->command->warn("Skipping purchase product ID {$purchaseProductData['id']} due to missing purchase ID {$purchaseProductData['purchase_id']}");
                    continue;
                }
                PurchaseProduct::create([
                    'id' => $purchaseProductData['id'],
                    'purchase_id' => $purchaseProductData['purchase_id'],
                    'product_id' => $purchaseProductData['product_id'],
                    'variant_id' => $purchaseProductData['variant_id'],
                    'attributes' => $purchaseProductData['attributes'] ? json_decode($purchaseProductData['attributes'], true) : null,
                    'stocks' => $purchaseProductData['stocks'],
                    'buy_price' => $purchaseProductData['buy_price'],
                    'sell_price' => $purchaseProductData['sell_price'],
                    'total' => $purchaseProductData['total'],
                    'created_at' => $purchaseProductData['created_at'],
                    'updated_at' => $purchaseProductData['updated_at'],
                ]);
                $imported++;
            } catch (\Exception $e) {
                $this->command->warn("Failed to import purchase product ID {$purchaseProductData['id']}: " . $e->getMessage());
            }
        }

        $this->command->info("Purchase products: {$imported} imported");
    }

    private function importOrders(): void
    {
        $this->command->info('Importing orders...');
        
        $ordersData = json_decode(file_get_contents(public_path('myint/orders.json')), true);
        $imported = 0;
        $skipped = 0;

        foreach ($ordersData as $orderData) {
            // Skip records with deleted_at not null
            if (!is_null($orderData['deleted_at'])) {
                $skipped++;
                continue;
            }

            try {
                Order::create([
                    'id' => $orderData['id'],
                    'invoice_number' => $orderData['invoice_number'],
                    'seller_id' => 1, // Default seller_id
                    'tenant_id' => 1, // Default tenant_id
                    'staff_id' => $orderData['staff_id'] ? 1 : null, // Default staff_id or null
                    'walk_in_customer' => (bool) $orderData['walk_in_customer'],
                    'is_cod' => (bool) $orderData['is_cod'],
                    'user_id' => $orderData['user_id'],
                    'user_address_id' => $orderData['user_address_id'],
                    'payment_id' => null,
                    'payment_account_id' => null,
                    'status' => $orderData['status'],
                    'sub_total' => $orderData['sub_total'],
                    'delivery_fees' => $orderData['delivery_fees'],
                    'total_price' => $orderData['total_price'],
                    'payment_slip' => $orderData['payment_slip'],
                    'tax' => $orderData['tax'],
                    'tax_price' => $orderData['tax_price'],
                    'given_amount' => $orderData['given_amount'],
                    'change_amount' => $orderData['change_amount'],
                    'note' => $orderData['note'],
                    'created_at' => $orderData['created_at'],
                    'updated_at' => $orderData['updated_at'],
                ]);
                $imported++;
            } catch (\Exception $e) {
                $this->command->warn("Failed to import order ID {$orderData['id']}: " . $e->getMessage());
            }
        }

        $this->command->info("Orders: {$imported} imported, {$skipped} skipped");
    }

    private function importOrderProducts(): void
    {
        $this->command->info('Importing order products...');
        
        // Get list of imported order IDs to check foreign key constraints
        $importedOrderIds = Order::pluck('id')->toArray();
        $importedProductIds = Product::pluck('id')->toArray();
        
        $orderProductsData = json_decode(file_get_contents(public_path('myint/order_products.json')), true);
        $imported = 0;
        $skipped = 0;

        foreach ($orderProductsData as $orderProductData) {
            // Skip records with invalid foreign key references
            if (!in_array($orderProductData['order_id'], $importedOrderIds) ||
                !in_array($orderProductData['product_id'], $importedProductIds)) {
                $skipped++;
                continue;
            }

            try {
                OrderProduct::create([
                    'id' => $orderProductData['id'],
                    'order_id' => $orderProductData['order_id'],
                    'product_id' => $orderProductData['product_id'],
                    'variant_id' => $orderProductData['variant_id'],
                    'attributes' => $orderProductData['attributes'] ? json_decode($orderProductData['attributes'], true) : null,
                    'price' => $orderProductData['price'],
                    'discount_price' => $orderProductData['discount_price'],
                    'discount' => $orderProductData['discount'],
                    'quantity' => $orderProductData['quantity'],
                    'profit' => $orderProductData['profit'],
                    'original_buy_price' => $orderProductData['original_buy_price'],
                    'original_price' => $orderProductData['original_price'],
                    'original_discount_price' => $orderProductData['original_discount_price'],
                    'currency_id' => $orderProductData['currency_id'],
                    'exchange_rate' => $orderProductData['exchange_rate'],
                    'sub_price' => $orderProductData['sub_price'],
                    'is_returned' => $orderProductData['is_returned'] ?? false,
                    'returned_quantity' => $orderProductData['returned_quantity'] ?? 0,
                    'total_refunded_amount' => $orderProductData['total_refunded_amount'] ?? 0,
                    'created_at' => $orderProductData['created_at'],
                    'updated_at' => $orderProductData['updated_at'],
                ]);
                $imported++;
            } catch (\Exception $e) {
                $this->command->warn("Failed to import order product ID {$orderProductData['id']}: " . $e->getMessage());
            }
        }

        $this->command->info("Order products: {$imported} imported, {$skipped} skipped");
    }
}
