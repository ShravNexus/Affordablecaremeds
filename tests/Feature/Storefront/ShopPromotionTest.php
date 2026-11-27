<?php

namespace Tests\Feature\Storefront;

use App\Models\Coupon;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ShopPromotionTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_first_live_public_coupon_is_rendered_as_the_shop_promotion(): void
    {
        Coupon::factory()->percent(20, 200)->create([
            'code' => 'LATER20',
            'description' => '20% off larger orders',
            'sort_order' => 20,
        ]);
        Coupon::factory()->percent(12, 99)->create([
            'code' => 'SAVE12',
            'description' => '12% off orders over $99',
            'ends_at' => '2026-10-01 12:00:00',
            'sort_order' => 10,
        ]);

        $response = $this->get('/shop');

        $response
            ->assertOk()
            ->assertSee('12% off orders over $99')
            ->assertSee('SAVE12')
            ->assertSee('data-promotion-ends-at="2026-10-01T12:00:00+00:00"', false)
            ->assertDontSee('LATER20');
    }

    public function test_a_coupon_without_an_end_time_has_no_fake_countdown(): void
    {
        Coupon::factory()->percent(10, 99)->create([
            'code' => 'SAVE10',
            'description' => null,
        ]);

        $response = $this->get('/shop');

        $response
            ->assertOk()
            ->assertSee('Seasonal offer —')
            ->assertSee('10% off — On orders over $99.00')
            ->assertDontSee('id="clock"', false)
            ->assertDontSee('data-promotion-ends-at=', false);
    }

    public function test_the_shop_hides_the_promotion_when_no_coupon_can_be_advertised(): void
    {
        Coupon::factory()->disabled()->create(['code' => 'DISABLED']);
        Coupon::factory()->private()->create(['code' => 'PRIVATE']);
        Coupon::factory()->expired()->create(['code' => 'EXPIRED']);

        $response = $this->get('/shop');

        $response
            ->assertOk()
            ->assertDontSee('id="promotion"', false)
            ->assertDontSee('DISABLED')
            ->assertDontSee('PRIVATE')
            ->assertDontSee('EXPIRED');
    }

    public function test_the_side_panel_shows_the_offer_and_categories_on_pages_other_than_home(): void
    {
        Coupon::factory()->percent(10, 99)->create(['code' => 'SAVE10', 'description' => null]);
        $category = ProductCategory::factory()->create(['slug' => 'sleep-recovery']);
        Product::factory()->create(['category_id' => $category->id]);

        $this->get(route('about'))
            ->assertOk()
            ->assertSee('class="side-panel"', false)
            ->assertSee('SAVE10')
            ->assertSee(route('product-category', ['slug' => 'sleep-recovery']), false);

        $sidePanel = Str::betweenFirst($this->get(route('shop'))->getContent(), 'class="side-panel"', '</aside>');
        $this->assertStringContainsString('sleep-recovery', $sidePanel);
        $this->assertStringNotContainsString('All products', $sidePanel);

        foreach (['home', 'cart', 'checkout'] as $routeName) {
            $this->get(route($routeName))
                ->assertOk()
                ->assertDontSee('class="side-panel"', false)
                ->assertDontSee('id="promotion"', false);
        }
    }

    public function test_the_christmas_strip_advertises_the_live_coupon_on_every_page(): void
    {
        Coupon::factory()->percent(10, 99)->create([
            'code' => 'XMAS10',
            'description' => null,
            'ends_at' => now()->addDays(3),
        ]);

        foreach (['home', 'cart', 'about'] as $routeName) {
            $this->get(route($routeName))
                ->assertOk()
                ->assertSee('Merry Christmas!')
                ->assertSee('<span class="xmas__code">XMAS10</span>', false)
                ->assertSee('Ends in')
                ->assertSee('href="'.route('shop').'" class="xmas__btn"', false);
        }
    }

    public function test_the_christmas_strip_counts_down_to_christmas_when_the_offer_has_no_end(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Merry Christmas!')
            ->assertSee('Christmas in')
            ->assertDontSee('Ends in')
            ->assertDontSee('xmas__code', false);
    }

    public function test_the_side_panel_starts_below_the_breadcrumb(): void
    {
        $product = Product::factory()->create();

        $this->get(route('product', $product->slug))
            ->assertOk()
            ->assertSeeInOrder(['class="crumb"', 'class="shell page-side"'], false);
    }

    public function test_the_shop_escapes_a_manager_supplied_promotion_description(): void
    {
        Coupon::factory()->create([
            'code' => 'SAFE10',
            'description' => '<script>alert("promotion")</script>',
        ]);

        $response = $this->get('/shop');

        $response
            ->assertOk()
            ->assertSee('&lt;script&gt;', false)
            ->assertDontSee('<script>alert("promotion")</script>', false);
    }
}
