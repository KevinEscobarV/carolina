<?php

namespace Tests\Feature\Http\Middleware;

use Tests\TestCase;

class RedirectToAppHostTest extends TestCase
{
    public function test_another_name_of_the_site_goes_to_the_same_page_on_the_app_host(): void
    {
        config(['app.url' => 'https://carolina.test']);

        $this->get('https://www.carolina.test/login?from=bookmark')
            ->assertStatus(301)
            ->assertRedirect('https://carolina.test/login?from=bookmark');
    }

    public function test_plain_http_request_to_another_name_gets_there_in_a_single_redirect(): void
    {
        config(['app.url' => 'https://carolina.test']);

        $this->get('http://www.carolina.test/login')
            ->assertStatus(301)
            ->assertRedirect('https://carolina.test/login');
    }

    public function test_any_host_is_served_when_the_app_runs_over_http(): void
    {
        config(['app.url' => 'http://localhost']);

        $this->get('http://127.0.0.1/login')->assertOk();
    }
}
