<?php

namespace Tests\Feature\Http\Middleware;

use Tests\TestCase;

class RedirectToHttpsTest extends TestCase
{
    public function test_plain_http_request_goes_to_the_same_page_over_https_when_the_app_is_served_over_https(): void
    {
        config(['app.url' => 'https://carolina.test']);

        $this->get('http://carolina.test/login?from=search')
            ->assertStatus(301)
            ->assertRedirect('https://carolina.test/login?from=search');
    }

    public function test_request_over_https_is_served(): void
    {
        config(['app.url' => 'https://carolina.test']);

        $this->get('https://carolina.test/login')->assertOk();
    }

    public function test_plain_http_request_is_served_when_the_app_runs_over_http(): void
    {
        config(['app.url' => 'http://localhost']);

        $this->get('http://localhost/login')->assertOk();
    }
}
