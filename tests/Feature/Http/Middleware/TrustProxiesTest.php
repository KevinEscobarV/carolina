<?php

namespace Tests\Feature\Http\Middleware;

use Illuminate\Http\Request;
use Tests\TestCase;

class TrustProxiesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config(['app.url' => 'https://carolina.test']);
    }

    protected function tearDown(): void
    {
        // Symfony keeps the trusted proxies in a static property across requests
        Request::setTrustedProxies([], Request::HEADER_X_FORWARDED_FOR);

        parent::tearDown();
    }

    public function test_forwarded_https_header_from_a_client_is_ignored_when_no_proxy_is_trusted(): void
    {
        config(['trustedproxy.proxies' => null]);

        $this->get('http://carolina.test/login', ['X-Forwarded-Proto' => 'https'])
            ->assertStatus(301)
            ->assertRedirect('https://carolina.test/login');
    }

    public function test_request_forwarded_as_https_by_a_trusted_proxy_is_served(): void
    {
        config(['trustedproxy.proxies' => '*']);

        $this->get('http://carolina.test/login', ['X-Forwarded-Proto' => 'https'])->assertOk();
    }
}
