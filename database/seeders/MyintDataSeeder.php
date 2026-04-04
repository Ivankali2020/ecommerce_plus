<?php

namespace Database\Seeders;

use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Currency;
use App\Models\Exchange;
use App\Models\Expanse;
use App\Models\ExpanseCategory;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Payment;
use App\Models\PaymentAccount;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseProduct;
use App\Models\Seller;
use App\Models\Supplier;
use App\Models\Tenant;
use App\Models\User;
use App\Models\UserAddress;
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
                'domain' => 'ab',
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

            $exchange = new Exchange;
            $exchange->currency_id = $currency->id;
            $exchange->rate = '4500';
            $exchange->seller_id = $user['id'];
            $exchange->tenant_id = $tenant->id;
            $exchange->save();
            // Import in order of dependencies
            $this->importAttributes($user['id'], $tenant->id);
            $this->importAttributeValues($user['id'], $tenant->id);
            $this->importBrands($user['id'], $tenant->id);
            $this->importCategories($user['id'], $tenant->id);
            $this->importSuppliers($user['id'], $tenant->id);
            $this->importProducts($user['id'], $tenant->id, $exchange->id);
            $this->importExpanseCategories($user['id'], $tenant->id);
            $this->importExpanses($user['id'], $tenant->id);
            $this->importOrders($user['id'], $tenant->id);
            $this->importOrderProducts($user['id'], $tenant->id);
            $this->importPurchases($user['id'], $tenant->id);
            $this->importPurchaseProducts($user['id'], $tenant->id);

            DB::commit();
            $this->command->info('Myint data import completed successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Import failed: '.$e->getMessage());
            Log::error('Myint data import failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    private function importAttributes(int $sellerId, int $tenantId): void
    {
        $this->command->info('Importing attributes...');

        $attributesData = json_decode(file_get_contents(public_path('myint/attributes.json')), true);
        $imported = 0;
        $skipped = 0;

        foreach ($attributesData as $attributeData) {
            // Skip records with deleted_at not null
            if (! is_null($attributeData['deleted_at'])) {
                $skipped++;

                continue;
            }

            try {
                $attribute = new Attribute([
                    'id' => $attributeData['id'],
                    'seller_id' => $sellerId,
                    'tenant_id' => $tenantId,
                    'name' => $attributeData['name'],
                    'type' => $attributeData['type'],
                    'created_at' => $attributeData['created_at'],
                    'updated_at' => $attributeData['updated_at'],
                ]);
                $attribute->saveQuietly();
                $imported++;
            } catch (\Exception $e) {
                $this->command->warn("Failed to import attribute ID {$attributeData['id']}: ".$e->getMessage());
            }
        }

        $this->command->info("Attributes: {$imported} imported, {$skipped} skipped");
    }

    private function importAttributeValues(int $sellerId, int $tenantId): void
    {
        $this->command->info('Importing attribute values...');

        // Get list of imported attribute IDs to check foreign key constraints
        $importedAttributeIds = Attribute::pluck('id')->toArray();

        $attributeValuesData = json_decode(file_get_contents(public_path('myint/attribute_values.json')), true);
        $imported = 0;
        $skipped = 0;

        foreach ($attributeValuesData as $attributeValueData) {
            // Skip records with deleted_at not null, incomplete data, or invalid attribute_id reference
            if (! is_null($attributeValueData['deleted_at'] ?? null) ||
                ! isset($attributeValueData['id']) ||
                ! isset($attributeValueData['attribute_id']) ||
                ! in_array($attributeValueData['attribute_id'], $importedAttributeIds)) {
                $skipped++;

                continue;
            }

            try {
                $attributeValue = new AttributeValue([
                    'id' => $attributeValueData['id'],
                    'attribute_id' => $attributeValueData['attribute_id'],
                    'value' => $attributeValueData['value'],
                    'color_code' => $attributeValueData['color_code'],
                    'created_at' => $attributeValueData['created_at'] ?? now(),
                    'updated_at' => $attributeValueData['updated_at'] ?? now(),
                ]);
                $attributeValue->saveQuietly();
                $imported++;
            } catch (\Exception $e) {
                $this->command->warn("Failed to import attribute value ID {$attributeValueData['id']}: ".$e->getMessage());
            }
        }

        $this->command->info("Attribute values: {$imported} imported, {$skipped} skipped");
    }

    private function importBrands(int $sellerId, int $tenantId): void
    {
        $this->command->info('Importing brands...');

        $brandsData = json_decode(file_get_contents(public_path('myint/brands.json')), true);
        $imported = 0;
        $skipped = 0;

        foreach ($brandsData as $brandData) {
            // Skip records with deleted_at not null
            if (! is_null($brandData['deleted_at']) || $brandData['seller_id'] == 1) {
                $skipped++;

                continue;
            }

            try {
                $brand = new Brand([
                    'id' => $brandData['id'],
                    'seller_id' => $sellerId,
                    'tenant_id' => $tenantId,
                    'name' => $brandData['name'],
                    'description' => $brandData['description'],
                    'created_at' => $brandData['created_at'],
                    'updated_at' => $brandData['updated_at'],
                ]);
                $brand->saveQuietly();
                $imported++;
            } catch (\Exception $e) {
                $this->command->warn("Failed to import brand ID {$brandData['id']}: ".$e->getMessage());
            }
        }

        $this->command->info("Brands: {$imported} imported, {$skipped} skipped");
    }

    private function importCategories(int $sellerId, int $tenantId): void
    {
        $this->command->info('Importing categories...');

        $categoriesData = json_decode(file_get_contents(public_path('myint/categories.json')), true);
        $imported = 0;
        $skipped = 0;

        foreach ($categoriesData as $categoryData) {
            // Skip records with deleted_at not null
            if (! is_null($categoryData['deleted_at']) || $categoryData['seller_id'] == 1) {
                $skipped++;

                continue;
            }

            try {
                $category = new Category([
                    'id' => $categoryData['id'],
                    'seller_id' => $sellerId,
                    'tenant_id' => $tenantId,
                    'parent_id' => $categoryData['parent_id'],
                    'description' => $categoryData['description'],
                    'name' => $categoryData['name'],
                    'is_sub_category' => (bool) $categoryData['is_sub_category'],
                    'created_at' => $categoryData['created_at'],
                    'updated_at' => $categoryData['updated_at'],
                ]);
                $category->saveQuietly();
                $imported++;
            } catch (\Exception $e) {
                $this->command->warn("Failed to import category ID {$categoryData['id']}: ".$e->getMessage());
            }
        }

        $this->command->info("Categories: {$imported} imported, {$skipped} skipped");
    }

    private function importProducts(int $sellerId, int $tenantId, int $exchangeId): void
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
            if (! is_null($productData['deleted_at']) ||
                $productData['seller_id'] == 1 ||
                ($productData['brand_id'] && ! in_array($productData['brand_id'], $importedBrandIds)) ||
                ! in_array($productData['category_id'], $importedCategoryIds)) {
                $skipped++;

                continue;
            }

            try {
                $product = new Product([
                    'id' => $productData['id'],
                    'seller_id' => $sellerId,
                    'tenant_id' => $tenantId,
                    'supplier_id' => $productData['supplier_id'],
                    'brand_id' => $productData['brand_id'],
                    'category_id' => $productData['category_id'],
                    'exchange_id' => $exchangeId,
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
                $product->saveQuietly();
                $imported++;
            } catch (\Exception $e) {
                $this->command->warn("Failed to import product ID {$productData['id']}: ".$e->getMessage());
            }
        }

        $this->command->info("Products: {$imported} imported, {$skipped} skipped");
    }

    private function importPurchases(int $sellerId, int $tenantId): void
    {
        $this->command->info('Importing purchases...');

        $purchasesData = json_decode(file_get_contents(public_path('myint/purchases.json')), true);
        $imported = 0;

        foreach ($purchasesData as $purchaseData) {
            try {
                $purchase = new Purchase([
                    'id' => $purchaseData['id'],
                    'seller_id' => $sellerId,
                    'tenant_id' => $tenantId,
                    'supplier_id' => $purchaseData['supplier_id'],
                    'status' => $purchaseData['status'],
                    'total_amount' => $purchaseData['total_amount'],
                    'paid_amount' => $purchaseData['paid_amount'],
                    'remaining_amount' => $purchaseData['remaining_amount'],
                    'created_at' => $purchaseData['created_at'],
                    'updated_at' => $purchaseData['updated_at'],
                ]);
                $purchase->saveQuietly();
                $imported++;
            } catch (\Exception $e) {
                $this->command->warn("Failed to import purchase ID {$purchaseData['id']}: ".$e->getMessage());
            }
        }

        $this->command->info("Purchases: {$imported} imported");
    }

    private function importPurchaseProducts(int $sellerId, int $tenantId): void
    {
        $this->command->info('Importing purchase products...');

        $purchaseProductsData = json_decode(file_get_contents(public_path('myint/purchase_products.json')), true);
        $imported = 0;

        $purchaseProductsDataIDs = Purchase::pluck('id')->toArray();

        foreach ($purchaseProductsData as $purchaseProductData) {
            try {
                if (! in_array($purchaseProductData['purchase_id'], $purchaseProductsDataIDs)) {
                    $this->command->warn("Skipping purchase product ID {$purchaseProductData['id']} due to missing purchase ID {$purchaseProductData['purchase_id']}");

                    continue;
                }
                $purchaseProduct = new PurchaseProduct([
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
                $purchaseProduct->saveQuietly();
                $imported++;
            } catch (\Exception $e) {
                $this->command->warn("Failed to import purchase product ID {$purchaseProductData['id']}: ".$e->getMessage());
            }
        }

        $this->command->info("Purchase products: {$imported} imported");
    }

    private function importOrders(int $sellerId, int $tenantId): void
    {
        $this->command->info('Importing orders...');

        $ordersData = json_decode(file_get_contents(public_path('myint/orders.json')), true);
        $imported = 0;
        $skipped = 0;

        foreach ($ordersData as $orderData) {
            // Skip records with deleted_at not null
            if (! is_null($orderData['deleted_at'])) {
                $skipped++;

                continue;
            }

            try {
                $order = new Order([
                    'id' => $orderData['id'],
                    'invoice_number' => $orderData['invoice_number'],
                    'seller_id' => $sellerId,
                    'tenant_id' => $tenantId,
                    'staff_id' => null, // Use seller_id as staff_id or null
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
                $order->saveQuietly();
                $imported++;
            } catch (\Exception $e) {
                $this->command->warn("Failed to import order ID {$orderData['id']}: ".$e->getMessage());
            }
        }

        $this->command->info("Orders: {$imported} imported, {$skipped} skipped");
    }

    private function importOrderProducts(int $sellerId, int $tenantId): void
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
            if (! in_array($orderProductData['order_id'], $importedOrderIds) ||
                ! in_array($orderProductData['product_id'], $importedProductIds)) {
                $skipped++;

                continue;
            }

            try {
                $orderProduct = new OrderProduct([
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
                $orderProduct->saveQuietly();
                $imported++;
            } catch (\Exception $e) {
                $this->command->warn("Failed to import order product ID {$orderProductData['id']}: ".$e->getMessage());
            }
        }

        $this->command->info("Order products: {$imported} imported, {$skipped} skipped");
    }

    private function importSuppliers(int $sellerId, int $tenantId): void
    {
        $this->command->info('Importing suppliers...');

        $suppliersData = json_decode(file_get_contents(public_path('myint/suppliers.json')), true);
        $imported = 0;
        $skipped = 0;

        foreach ($suppliersData as $supplierData) {
            // Skip if township_id doesn't exist
            if (! isset($supplierData['township_id'])) {
                $skipped++;

                continue;
            }

            try {
                $supplier = new Supplier([
                    'id' => $supplierData['id'],
                    'seller_id' => $sellerId,
                    'tenant_id' => $tenantId,
                    'region_id' => 5,
                    'township_id' => $supplierData['township_id'],
                    'name' => $supplierData['name'],
                    'email' => $supplierData['email'],
                    'phone' => $supplierData['phone'],
                    'about' => $supplierData['about'],
                    'created_at' => $supplierData['created_at'],
                    'updated_at' => $supplierData['updated_at'],
                ]);
                $supplier->saveQuietly();
                $imported++;
            } catch (\Exception $e) {
                $this->command->warn("Failed to import supplier ID {$supplierData['id']}: ".$e->getMessage());
            }
        }

        $this->command->info("Suppliers: {$imported} imported, {$skipped} skipped");
    }

    private function importExpanseCategories(int $sellerId, int $tenantId): void
    {
        $this->command->info('Importing expanse categories...');

        $expanseCategoriesData = json_decode(file_get_contents(public_path('myint/expanse_categories.json')), true);
        $imported = 0;

        foreach ($expanseCategoriesData as $expanseCategoryData) {
            try {
                $expanseCategory = new ExpanseCategory([
                    'id' => $expanseCategoryData['id'],
                    'name' => $expanseCategoryData['name'],
                    'icon' => $expanseCategoryData['icon'],
                    'seller_id' => $sellerId,
                    'tenant_id' => $tenantId,
                    'created_at' => $expanseCategoryData['created_at'],
                    'updated_at' => $expanseCategoryData['updated_at'],
                ]);
                $expanseCategory->saveQuietly();
                $imported++;
            } catch (\Exception $e) {
                $this->command->warn("Failed to import expanse category ID {$expanseCategoryData['id']}: ".$e->getMessage());
            }
        }

        $this->command->info("Expanse categories: {$imported} imported");
    }

    private function importExpanses(int $sellerId, int $tenantId): void
    {
        $this->command->info('Importing expanses...');

        $expansesData = json_decode(file_get_contents(public_path('myint/expanses.json')), true);
        $imported = 0;
        $skipped = 0;

        // Get list of imported expanse category IDs to check foreign key constraints
        $importedExpanseCategoryIds = ExpanseCategory::pluck('id')->toArray();

        foreach ($expansesData as $expanseData) {
            // Skip if expanse_category_id doesn't exist in imported data
            if (! isset($expanseData['expanse_category_id']) || ! in_array($expanseData['expanse_category_id'], $importedExpanseCategoryIds)) {
                $skipped++;

                continue;
            }

            try {
                $expanse = new Expanse([
                    'id' => $expanseData['id'],
                    'expanse_category_id' => $expanseData['expanse_category_id'],
                    'amount' => $expanseData['amount'],
                    'description' => $expanseData['description'],
                    'payment_method' => $expanseData['payment_method'],
                    'seller_id' => $sellerId,
                    'tenant_id' => $tenantId,
                    'created_at' => $expanseData['created_at'],
                    'updated_at' => $expanseData['updated_at'],
                ]);
                $expanse->saveQuietly();
                $imported++;
            } catch (\Exception $e) {
                $this->command->warn("Failed to import expanse ID {$expanseData['id']}: ".$e->getMessage());
            }
        }

        $this->command->info("Expanses: {$imported} imported, {$skipped} skipped");
    }
}
