<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class CountryAccessTest extends TestCase
{
    use RefreshDatabase;

    #[TestWith(['49.36.0.1'])]
    #[TestWith(['2405:200::1'])]
    #[TestWith(['::ffff:49.36.0.1'])]
    public function test_visitors_from_india_see_the_country_restriction_page(string $address): void
    {
        $this->withServerVariables(['REMOTE_ADDR' => $address])
            ->get('/')
            ->assertForbidden()
            ->assertSeeText('This website is not available in your country.')
            ->assertHeaderContains('Cache-Control', 'no-store');
    }

    #[TestWith(['127.0.0.1'])]
    #[TestWith(['::1'])]
    #[TestWith(['8.8.8.8'])]
    public function test_localhost_and_non_indian_visitors_can_open_the_storefront(string $address): void
    {
        $this->withServerVariables(['REMOTE_ADDR' => $address])
            ->get(route('about'))
            ->assertOk();
    }

    public function test_country_restriction_applies_to_storefront_api_requests(): void
    {
        $this->withServerVariables(['REMOTE_ADDR' => '49.36.0.1'])
            ->get(route('api.storefront.search.suggest'))
            ->assertForbidden()
            ->assertJsonPath('message', 'This website is not available in your country.')
            ->assertHeaderContains('Cache-Control', 'no-store');
    }

    public function test_untrusted_forwarding_headers_do_not_override_the_client_ip(): void
    {
        $this->withServerVariables(['REMOTE_ADDR' => '49.36.0.1'])
            ->withHeaders(['X-Forwarded-For' => '8.8.8.8', 'CF-IPCountry' => 'US'])
            ->get('/')
            ->assertForbidden();
    }

    public function test_the_client_ip_is_used_behind_a_configured_reverse_proxy(): void
    {
        config(['trustedproxy.proxies' => '10.0.0.2']);

        $this->withServerVariables(['REMOTE_ADDR' => '10.0.0.2'])
            ->withHeader('X-Forwarded-For', '49.36.0.1')
            ->get('/')
            ->assertForbidden();
    }
}
