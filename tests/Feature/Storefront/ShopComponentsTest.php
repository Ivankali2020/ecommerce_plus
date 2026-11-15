<?php

declare(strict_types=1);

use App\Livewire\CartNavItem;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;

/**
 * Builds an unsaved product with every relation the product card reads, so rendering never hits the database.
 *
 * @param  array<string, mixed>  $attributes
 */
function makeStorefrontProduct(array $attributes = []): Product
{
    $product = new Product;
    $product->forceFill(array_merge([
        'id' => 42,
        'name' => 'Linen Shirt',
        'has_variant' => false,
        'mmk_price' => 15000,
        'mmk_discount_price' => 12000,
        'discount' => 0,
        'quantity' => 8,
        'MOQ' => 2,
    ], $attributes));

    $category = new Category;
    $category->forceFill(['id' => 1, 'name' => 'Apparel']);

    return $product
        ->setRelation('media', new Collection)
        ->setRelation('variants', new Collection)
        ->setRelation('category', $category);
}

beforeEach(function () {
    // Tenant storefront routes are only registered on tenant domains, so stub the ones the components link to.
    Route::get('product/{product}', fn () => '')->name('product');
    Route::get('cart', fn () => '')->name('cart');
    Route::getRoutes()->refreshNameLookups();
});

test('price shows the formatted amount and only strikes through a higher original price', function () {
    $this->blade('<x-shop.price :amount="12000" :original="15000" />')
        ->assertSee('12,000')
        ->assertSee('15,000')
        ->assertSee('line-through', false);

    $this->blade('<x-shop.price :amount="12000" :original="12000" />')
        ->assertSee('12,000')
        ->assertDontSee('line-through', false);
});

test('button renders a link when given an href and a typed button otherwise', function () {
    $this->blade('<x-shop.button href="/cart">Cart</x-shop.button>')
        ->assertSee('<a href="/cart"', false);

    $this->blade('<x-shop.button>Save</x-shop.button>')
        ->assertSee('type="button"', false);

    $this->blade('<x-shop.button type="submit">Send</x-shop.button>')
        ->assertSee('type="submit"', false)
        ->assertDontSee('type="button"', false);
});

test('chip marks the active state and shows an optional count', function () {
    $this->blade('<x-shop.chip :active="true" :count="7">Shoes</x-shop.chip>')
        ->assertSee('Shoes')
        ->assertSee('7')
        ->assertSee('bg-ink', false);

    $this->blade('<x-shop.chip>Bags</x-shop.chip>')
        ->assertDontSee('bg-ink text-canvas', false);
});

test('attribute chips list each attribute with its values', function () {
    $this->blade('<x-shop.attribute-chips :items="$items" />', [
        'items' => [['attribute' => 'Color', 'value' => ['Red', 'Blue']]],
    ])
        ->assertSee('Color:')
        ->assertSee('Red, Blue');

    expect(trim((string) $this->blade('<x-shop.attribute-chips :items="[]" />')))->toBe('');
});

test('product card shows discount, category, MOQ and stock for an in-stock product', function () {
    $product = makeStorefrontProduct(['discount' => 20]);

    $this->blade('<x-shop.product-card :product="$product" />', ['product' => $product])
        ->assertSee('Linen Shirt')
        ->assertSee('Apparel')
        ->assertSee('−20%')
        ->assertSee('12,000')
        ->assertSee('15,000')
        ->assertSee(__('messages.moq').' 2')
        ->assertSee('8 '.__('messages.in_stock'))
        ->assertSee('wire:click="addToCart(42)"', false)
        ->assertDontSee(__('messages.out_of_stock'));
});

test('product card disables add to cart when the product is out of stock', function () {
    $product = makeStorefrontProduct(['quantity' => 0]);

    $this->blade('<x-shop.product-card :product="$product" />', ['product' => $product])
        ->assertSee(__('messages.out_of_stock'))
        ->assertSee('disabled="disabled"', false);
});

test('product card labels wholesale products', function () {
    $product = makeStorefrontProduct();

    $this->blade('<x-shop.product-card :product="$product" type="wholesale" />', ['product' => $product])
        ->assertSee(__('messages.wholesale.wholesale'));
});

test('empty state renders its title, description and action slot', function () {
    $this->blade(
        '<x-shop.empty-state icon="heroicon-o-shopping-bag" title="Nothing here" description="Add something"><a href="/">Shop</a></x-shop.empty-state>'
    )
        ->assertSee('Nothing here')
        ->assertSee('Add something')
        ->assertSee('<a href="/">Shop</a>', false);
});

test('star rating fills stars up to the rounded rating', function () {
    $html = (string) $this->blade('<x-shop.star-rating :rating="3.6" />');

    expect(substr_count($html, 'text-accent'))->toBe(4)
        ->and(substr_count($html, 'text-line'))->toBe(1);
});

test('social icon renders only known networks', function () {
    $this->blade('<x-shop.social-icon network="facebook" />')->assertSee('<svg', false);

    expect(trim((string) $this->blade('<x-shop.social-icon network="myspace" />')))->toBe('');
});

test('cart nav item renders the header icon or the mobile tab depending on its variant', function () {
    Livewire::test(CartNavItem::class)
        ->assertSet('variant', 'desktop')
        ->assertSee('aria-label="'.__('messages.navigation.cart').'"', false)
        ->assertDontSee('flex-1 flex-col', false);

    Livewire::test(CartNavItem::class, ['variant' => 'mobile'])
        ->assertSee(__('messages.navigation.cart'))
        ->assertSee('flex-1 flex-col', false);
});

test('only the active chip is marked for the rail to scroll into view', function () {
    $this->blade('<x-shop.chip :active="true">Shoes</x-shop.chip>')->assertSee('data-active', false);
    $this->blade('<x-shop.chip>Bags</x-shop.chip>')->assertDontSee('data-active', false);
});

test('chip rail wraps its chips with slider arrows and an expand toggle', function () {
    $this->blade('<x-shop.chip-rail><x-shop.chip>Shoes</x-shop.chip><x-shop.chip>Bags</x-shop.chip></x-shop.chip-rail>')
        ->assertSeeInOrder(['Shoes', 'Bags'])
        ->assertSee('x-ref="track"', false)
        ->assertSee('@click="slide(-1)"', false)
        ->assertSee('@click="slide(1)"', false)
        ->assertSee('@click="toggle()"', false)
        ->assertSee('aria-label="'.__('messages.view_all').'"', false);
});
