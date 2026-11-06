<?php

namespace Tests\Feature\Livewire\Setting\Index;

use App\Livewire\Setting\Index\Page;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\Concerns\CreatesPromises;
use Tests\TestCase;

class PageTest extends TestCase
{
    use CreatesPromises, RefreshDatabase;

    public function test_saving_stores_the_grace_days_of_the_campaign_and_the_usury_rate(): void
    {
        $this->signInToCampaign();

        Livewire::test(Page::class)
            ->assertSet('usury_rate', Setting::DEFAULT_USURY_RATE)
            ->set('grace_days', 5)
            ->set('usury_rate', '27.5')
            ->call('saveArrears')
            ->assertHasNoErrors();

        $this->assertSame(5, Setting::graceDays($this->campaign->id));
        $this->assertSame('27.5', Setting::usuryRate()->value);
    }

    public function test_grace_days_must_be_a_whole_number_of_days(): void
    {
        $this->signInToCampaign();

        Livewire::test(Page::class)
            ->set('grace_days', -1)
            ->call('saveArrears')
            ->assertHasErrors(['grace_days' => 'min']);
    }
}
