<?php

namespace Tests\Feature;

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

    public function test_a_missing_file_falls_back_to_the_plain_asset_url(): void
    {
        $this->assertSame(
            asset('css/does-not-exist.css'),
            VersionedAsset::url('css/does-not-exist.css'),
        );
    }
}
