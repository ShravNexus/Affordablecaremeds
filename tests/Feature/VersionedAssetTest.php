<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Support\VersionedAsset;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VersionedAssetTest extends TestCase
{
    use RefreshDatabase;

    public function test_storefront_pages_link_the_shared_stylesheet_with_its_file_version(): void
    {
        $version = filemtime(public_path('css/common.css'));

        $this->get(route('about'))
            ->assertOk()
            ->assertSee(asset('css/common.css').'?v='.$version, false);
    }

    public function test_storefront_pages_load_bootstrap_and_fonts_from_public_assets(): void
    {
        $this->get(route('about'))
            ->assertSee('href="'.asset('css/fonts/storefront.css').'?v=', false)
            ->assertSee('href="'.asset('css/vendor/bootstrap-5.3.3.min.css').'?v=', false)
            ->assertDontSee('fonts.googleapis.com');
    }

    public function test_manager_dashboard_loads_bootstrap_icons_and_fonts_from_public_assets(): void
    {
        $admin = Admin::factory()->create();

        $this->actingAs($admin, 'admin')->get(route('manager.dashboard'))
            ->assertSee('href="'.asset('css/fonts/manager.css').'?v=', false)
            ->assertSee('href="'.asset('css/vendor/bootstrap-5.3.3.min.css').'?v=', false)
            ->assertSee('href="'.asset('css/vendor/bootstrap-icons-1.11.3.min.css').'?v=', false)
            ->assertDontSee('fonts.googleapis.com');
    }

    public function test_manager_login_loads_bootstrap_icons_and_fonts_from_public_assets(): void
    {
        $this->get(route('manager.login'))
            ->assertSee('href="'.asset('css/fonts/manager-auth.css').'?v=', false)
            ->assertSee('href="'.asset('css/vendor/bootstrap-5.3.3.min.css').'?v=', false)
            ->assertSee('href="'.asset('css/vendor/bootstrap-icons-1.11.3.min.css').'?v=', false)
            ->assertDontSee('fonts.googleapis.com');
    }

    public function test_a_missing_file_falls_back_to_the_plain_asset_url(): void
    {
        $this->assertSame(
            asset('css/does-not-exist.css'),
            VersionedAsset::url('css/does-not-exist.css'),
        );
    }
}
