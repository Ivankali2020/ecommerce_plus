<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Exchange;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Variant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Seeds 100 wholesale products for seller_id=1 covering all possible situations:
 *
 *  Scenario A  – Simple wholesale, public, in-stock, no discount              (10 products)
 *  Scenario B  – Simple wholesale, public, in-stock, with discount             (10 products)
 *  Scenario C  – Simple wholesale, public, OUT-of-stock (quantity=0)           (10 products)
 *  Scenario D  – Simple wholesale, PRIVATE (is_public=false)                   (5  products)
 *  Scenario E  – Simple wholesale, with EXPIRED date (past)                    (5  products)
 *  Scenario F  – Simple wholesale, with FUTURE expiry (not expired yet)        (5  products)
 *  Scenario G  – Simple wholesale, returnable (is_return=true)                 (5  products)
 *  Scenario H  – Simple wholesale, shippable (is_shipped=true)                 (5  products)
 *  Scenario I  – Simple wholesale, recommended (is_recommended=true)           (5  products)
 *  Scenario J  – With VARIANTS: all variants wholesale                         (10 products, 2 variants each)
 *  Scenario K  – With VARIANTS: mixed (some wholesale, some retail)            (10 products, 2 variants each)
 *  Scenario L  – With VARIANTS: variants out-of-stock                          (5  products, 2 variants each)
 *  Scenario M  – With VARIANTS: discounted variants                            (5  products, 2 variants each)
 *  Scenario N  – Soft-deleted (trashed) wholesale products                     (5  products)
 *  Scenario O  – High-MOQ wholesale, no supplier, no brand                    (5  products)
 *
 * Total: 100 products (variants are additional rows in the variants table).
 */
class WholesaleProductSeeder extends Seeder
{
    private int $sellerId = 1;

    private string $tenantId = '1';

    /** @var array<int> */
    private array $categoryIds = [];

    /** @var array<int> */
    private array $brandIds = [];

    /** @var array<int> */
    private array $exchangeIds = [];

    private ?int $supplierId = null;

    public function run(): void
    {
        $this->loadLookups();

        $this->command->info('Seeding wholesale products for seller_id=1 …');

        $this->seedScenarioA();   // 10
        $this->seedScenarioB();   // 10
        $this->seedScenarioC();   // 10
        $this->seedScenarioD();   // 5
        $this->seedScenarioE();   // 5
        $this->seedScenarioF();   // 5
        $this->seedScenarioG();   // 5
        $this->seedScenarioH();   // 5
        $this->seedScenarioI();   // 5
        $this->seedScenarioJ();   // 10
        $this->seedScenarioK();   // 10
        $this->seedScenarioL();   // 5
        $this->seedScenarioM();   // 5
        $this->seedScenarioN();   // 5
        $this->seedScenarioO();   // 5

        $this->command->info('Done! 100 wholesale products seeded.');
    }

    // ─── Scenarios ───────────────────────────────────────────────────────────

    /** A: Simple wholesale, public, in-stock, no discount (10 products) */
    private function seedScenarioA(): void
    {
        for ($i = 1; $i <= 10; $i++) {
            Product::create($this->base([
                'name' => "WS-A{$i} Basic Wholesale Product",
                'is_public' => true,
                'is_whole_sale' => true,
                'has_variant' => false,
                'quantity' => rand(50, 500),
                'discount' => 0,
                'discount_price' => 0,
            ]));
        }
        $this->command->info('  Scenario A seeded (10 simple public in-stock, no discount)');
    }

    /** B: Simple wholesale, public, in-stock, with discount (10 products) */
    private function seedScenarioB(): void
    {
        for ($i = 1; $i <= 10; $i++) {
            $price = rand(5000, 50000);
            $discount = rand(5, 30);
            Product::create($this->base([
                'name' => "WS-B{$i} Discounted Wholesale Product",
                'is_public' => true,
                'is_whole_sale' => true,
                'has_variant' => false,
                'quantity' => rand(50, 300),
                'price' => $price,
                'discount' => $discount,
                'discount_price' => (int) ($price * (1 - $discount / 100)),
            ]));
        }
        $this->command->info('  Scenario B seeded (10 discounted public in-stock)');
    }

    /** C: Simple wholesale, public, OUT-of-stock (10 products) */
    private function seedScenarioC(): void
    {
        for ($i = 1; $i <= 10; $i++) {
            Product::create($this->base([
                'name' => "WS-C{$i} Out-of-Stock Wholesale Product",
                'is_public' => true,
                'is_whole_sale' => true,
                'has_variant' => false,
                'quantity' => 0,
            ]));
        }
        $this->command->info('  Scenario C seeded (10 public out-of-stock)');
    }

    /** D: Simple wholesale, PRIVATE (5 products) */
    private function seedScenarioD(): void
    {
        for ($i = 1; $i <= 5; $i++) {
            Product::create($this->base([
                'name' => "WS-D{$i} Private Wholesale Product",
                'is_public' => false,
                'is_whole_sale' => true,
                'has_variant' => false,
                'quantity' => rand(10, 100),
            ]));
        }
        $this->command->info('  Scenario D seeded (5 private / not public)');
    }

    /** E: Simple wholesale, EXPIRED date in the past (5 products) */
    private function seedScenarioE(): void
    {
        for ($i = 1; $i <= 5; $i++) {
            Product::create($this->base([
                'name' => "WS-E{$i} Expired Wholesale Product",
                'is_public' => true,
                'is_whole_sale' => true,
                'has_variant' => false,
                'quantity' => rand(1, 50),
                'expired_date' => now()->subDays(rand(1, 365)),
            ]));
        }
        $this->command->info('  Scenario E seeded (5 with past expired_date)');
    }

    /** F: Simple wholesale, FUTURE expiry (5 products) */
    private function seedScenarioF(): void
    {
        for ($i = 1; $i <= 5; $i++) {
            Product::create($this->base([
                'name' => "WS-F{$i} Future Expiry Wholesale Product",
                'is_public' => true,
                'is_whole_sale' => true,
                'has_variant' => false,
                'quantity' => rand(50, 200),
                'expired_date' => now()->addDays(rand(30, 730)),
            ]));
        }
        $this->command->info('  Scenario F seeded (5 with future expiry)');
    }

    /** G: Simple wholesale, returnable (5 products) */
    private function seedScenarioG(): void
    {
        for ($i = 1; $i <= 5; $i++) {
            Product::create($this->base([
                'name' => "WS-G{$i} Returnable Wholesale Product",
                'is_public' => true,
                'is_whole_sale' => true,
                'has_variant' => false,
                'is_return' => true,
                'quantity' => rand(20, 200),
            ]));
        }
        $this->command->info('  Scenario G seeded (5 returnable)');
    }

    /** H: Simple wholesale, shippable (5 products) */
    private function seedScenarioH(): void
    {
        for ($i = 1; $i <= 5; $i++) {
            Product::create($this->base([
                'name' => "WS-H{$i} Shippable Wholesale Product",
                'is_public' => true,
                'is_whole_sale' => true,
                'has_variant' => false,
                'is_shipped' => true,
                'quantity' => rand(20, 200),
            ]));
        }
        $this->command->info('  Scenario H seeded (5 shippable)');
    }

    /** I: Simple wholesale, recommended (5 products) */
    private function seedScenarioI(): void
    {
        for ($i = 1; $i <= 5; $i++) {
            Product::create($this->base([
                'name' => "WS-I{$i} Recommended Wholesale Product",
                'is_public' => true,
                'is_whole_sale' => true,
                'has_variant' => false,
                'is_recommended' => true,
                'quantity' => rand(50, 300),
            ]));
        }
        $this->command->info('  Scenario I seeded (5 recommended)');
    }

    /** J: has_variant=true, ALL variants are wholesale (10 products × 2 variants) */
    private function seedScenarioJ(): void
    {
        for ($i = 1; $i <= 10; $i++) {
            $product = Product::create($this->base([
                'name' => "WS-J{$i} All-Variant Wholesale Product",
                'is_public' => true,
                'is_whole_sale' => true,
                'has_variant' => true,
                'quantity' => 0, // quantity tracked per variant
            ]));

            foreach (['Small', 'Large'] as $size) {
                Variant::create($this->baseVariant($product->id, [
                    'name' => "{$product->name} – {$size}",
                    'is_whole_sale' => true,
                    'quantity' => rand(20, 150),
                ]));
            }
        }
        $this->command->info('  Scenario J seeded (10 products, all variants wholesale)');
    }

    /** K: has_variant=true, MIXED variants (1 wholesale + 1 retail per product) (10 products × 2 variants) */
    private function seedScenarioK(): void
    {
        for ($i = 1; $i <= 10; $i++) {
            $product = Product::create($this->base([
                'name' => "WS-K{$i} Mixed Variant Wholesale Product",
                'is_public' => true,
                'is_whole_sale' => true,
                'has_variant' => true,
                'quantity' => 0,
            ]));

            Variant::create($this->baseVariant($product->id, [
                'name' => "{$product->name} – Wholesale",
                'is_whole_sale' => true,
                'quantity' => rand(20, 100),
            ]));

            Variant::create($this->baseVariant($product->id, [
                'name' => "{$product->name} – Retail",
                'is_whole_sale' => false,
                'quantity' => rand(5, 50),
            ]));
        }
        $this->command->info('  Scenario K seeded (10 products, mixed wholesale/retail variants)');
    }

    /** L: has_variant=true, ALL variants OUT-of-stock (5 products × 2 variants) */
    private function seedScenarioL(): void
    {
        for ($i = 1; $i <= 5; $i++) {
            $product = Product::create($this->base([
                'name' => "WS-L{$i} Out-of-Stock Variant Wholesale Product",
                'is_public' => true,
                'is_whole_sale' => true,
                'has_variant' => true,
                'quantity' => 0,
            ]));

            foreach (['Type-A', 'Type-B'] as $type) {
                Variant::create($this->baseVariant($product->id, [
                    'name' => "{$product->name} – {$type}",
                    'is_whole_sale' => true,
                    'quantity' => 0,
                ]));
            }
        }
        $this->command->info('  Scenario L seeded (5 products, all variants out-of-stock)');
    }

    /** M: has_variant=true, DISCOUNTED wholesale variants (5 products × 2 variants) */
    private function seedScenarioM(): void
    {
        for ($i = 1; $i <= 5; $i++) {
            $product = Product::create($this->base([
                'name' => "WS-M{$i} Discounted Variant Wholesale Product",
                'is_public' => true,
                'is_whole_sale' => true,
                'has_variant' => true,
                'quantity' => 0,
            ]));

            foreach (['Standard', 'Premium'] as $grade) {
                $price = rand(10000, 100000);
                $discount = rand(10, 25);
                Variant::create($this->baseVariant($product->id, [
                    'name' => "{$product->name} – {$grade}",
                    'is_whole_sale' => true,
                    'quantity' => rand(10, 80),
                    'price' => $price,
                    'discount' => $discount,
                    'discount_price' => (int) ($price * (1 - $discount / 100)),
                ]));
            }
        }
        $this->command->info('  Scenario M seeded (5 products, discounted wholesale variants)');
    }

    /** N: Soft-deleted (trashed) wholesale products (5 products) */
    private function seedScenarioN(): void
    {
        for ($i = 1; $i <= 5; $i++) {
            $product = Product::create($this->base([
                'name' => "WS-N{$i} Deleted Wholesale Product",
                'is_public' => true,
                'is_whole_sale' => true,
                'has_variant' => false,
                'quantity' => rand(10, 100),
            ]));
            $product->delete(); // soft-delete
        }
        $this->command->info('  Scenario N seeded (5 soft-deleted / trashed)');
    }

    /** O: High-MOQ, no supplier, no brand (5 products) */
    private function seedScenarioO(): void
    {
        for ($i = 1; $i <= 5; $i++) {
            Product::create($this->base([
                'name' => "WS-O{$i} High-MOQ No-Brand Wholesale Product",
                'is_public' => true,
                'is_whole_sale' => true,
                'has_variant' => false,
                'quantity' => rand(1000, 5000),
                'MOQ' => rand(100, 500),
                'supplier_id' => null,
                'brand_id' => null,
            ]));
        }
        $this->command->info('  Scenario O seeded (5 high-MOQ, no supplier/brand)');
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    /**
     * Base product attributes for seller_id=1.
     *
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function base(array $overrides = []): array
    {
        $price = $overrides['price'] ?? rand(2000, 80000);

        return array_merge([
            'seller_id' => $this->sellerId,
            'tenant_id' => $this->tenantId,
            'supplier_id' => $this->supplierId,
            'brand_id' => $this->randomItem($this->brandIds),
            'category_id' => $this->randomItem($this->categoryIds),
            'exchange_id' => $this->randomItem($this->exchangeIds),
            'name' => 'Wholesale Product',
            'description' => 'Auto-seeded wholesale product for testing all possible situations.',
            'sku' => 'WS-'.Str::upper(Str::random(8)),
            'MOQ' => rand(1, 20),
            'buy_price' => (int) ($price * 0.6),
            'price' => $price,
            'quantity' => rand(10, 300),
            'discount' => 0,
            'discount_price' => 0,
            'has_variant' => false,
            'is_public' => true,
            'is_whole_sale' => true,
            'is_recommended' => false,
            'is_return' => false,
            'is_shipped' => false,
            'expired_date' => null,
            'attributes' => null,
        ], $overrides);
    }

    /**
     * Base variant attributes tied to a product.
     *
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function baseVariant(int $productId, array $overrides = []): array
    {
        $price = $overrides['price'] ?? rand(2000, 80000);

        return array_merge([
            'product_id' => $productId,
            'name' => 'Wholesale Variant',
            'sku' => 'WSV-'.Str::upper(Str::random(8)),
            'MOQ' => rand(1, 20),
            'buy_price' => (int) ($price * 0.6),
            'price' => $price,
            'quantity' => rand(10, 150),
            'discount' => 0,
            'discount_price' => 0,
            'is_whole_sale' => true,
            'expired_date' => null,
            'attributes' => [],
        ], $overrides);
    }

    /** Load FK lookup IDs from the database. */
    private function loadLookups(): void
    {
        $this->categoryIds = Category::where('seller_id', $this->sellerId)->pluck('id')->toArray();
        $this->brandIds = Brand::where('seller_id', $this->sellerId)->pluck('id')->toArray();
        $this->exchangeIds = Exchange::pluck('id')->toArray();
        $this->supplierId = Supplier::where('seller_id', $this->sellerId)->value('id');

        if (empty($this->categoryIds)) {
            throw new \RuntimeException('No categories found for seller_id=1. Run CategorySeeder first.');
        }

        if (empty($this->exchangeIds)) {
            throw new \RuntimeException('No exchanges found. Run ExchangeSeeder first.');
        }
    }

    /** @param  array<int>  $items */
    private function randomItem(array $items): ?int
    {
        return empty($items) ? null : $items[array_rand($items)];
    }
}

// ## WholesaleProductSeeder — 100 Products for Seller ID 1

//    Scenario                                │ Description                                              │ Products                               │ Variants
//   ─────────────────────────────────────────┼──────────────────────────────────────────────────────────┼────────────────────────────────────────┼────────────────────────────────────────
//    A                                       │ Simple wholesale · public · in-stock · no discount       │ 10                                     │ —
//    B                                       │ Simple wholesale · public · in-stock · with discount     │ 10                                     │ —
//    C                                       │ Simple wholesale · public · out-of-stock (quantity=0)    │ 10                                     │ —
//    D                                       │ Simple wholesale · private (is_public=false)             │ 5                                      │ —
//    E                                       │ Simple wholesale · expired expired_date (past)           │ 5                                      │ —
//    F                                       │ Simple wholesale · future expiry                         │ 5                                      │ —
//    G                                       │ Simple wholesale · returnable (is_return=true)           │ 5                                      │ —
//    H                                       │ Simple wholesale · shippable (is_shipped=true)           │ 5                                      │ —
//    I                                       │ Simple wholesale · recommended (is_recommended=true)     │ 5                                      │ —
//    J                                       │ has_variant=true · all variants wholesale                │ 10                                     │ 20
//    K                                       │ has_variant=true · mixed: 1 wholesale + 1 retail variant │ 10                                     │ 20
//    L                                       │ has_variant=true · all variants out-of-stock             │ 5                                      │ 10
//    M                                       │ has_variant=true · discounted wholesale variants         │ 5                                      │ 10
//    N                                       │ Soft-deleted (deleted_at set) wholesale products         │ 5                                      │ —
//    O                                       │ High-MOQ · no brand · no supplier                        │ 5                                      │ —
//    Total                                   │                                                          │ 100                                    │ 60
