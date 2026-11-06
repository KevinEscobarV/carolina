<?php

namespace Tests\Feature\Livewire\Public\Buyer\Statement;

use App\Livewire\Public\Buyer\Statement\Lookup;
use App\Models\Buyer;
use App\Models\Category;
use App\Models\Promise;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class LookupTest extends TestCase
{
    use RefreshDatabase;

    public function test_empty_document_is_rejected(): void
    {
        Livewire::test(Lookup::class)
            ->set('document_number', '')
            ->call('explore')
            ->assertHasErrors(['document_number' => 'required'])
            ->assertSet('searched', false);
    }

    public function test_unknown_document_shows_the_not_found_message(): void
    {
        Livewire::test(Lookup::class)
            ->set('document_number', '999888777')
            ->call('explore')
            ->assertSet('promises', [])
            ->assertSee('No encontramos promesas con ese número de documento.');
    }

    public function test_signed_in_user_finds_promises_of_another_campaign(): void
    {
        $current = Category::create(['name' => 'Rivarca']);
        $other = Category::create(['name' => 'Rivarca II']);
        $user = User::factory()->create(['current_category_id' => $other->id]);
        $this->actingAs($user);
        $promise = Promise::create(['number' => 'PROMESA 7', 'signature_date' => now()]);
        $promise->buyers()->attach(Buyer::factory()->create(['document_number' => '1115918673']));
        $user->switchCategory($current);

        Livewire::test(Lookup::class)
            ->set('document_number', '1.115.918.673')
            ->call('explore')
            ->assertSee('PROMESA 7')
            ->assertSee('Rivarca II');
    }
}
