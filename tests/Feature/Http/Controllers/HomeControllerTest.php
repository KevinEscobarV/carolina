<?php

namespace Tests\Feature\Http\Controllers;

use App\Livewire\Public\Buyer\Statement\Lookup;
use App\Models\Block;
use App\Models\Buyer;
use App\Models\Category;
use App\Models\Parcel;
use App\Models\Promise;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class HomeControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    public function test_figures_add_up_every_campaign_and_count_a_buyer_once(): void
    {
        $this->seedTwoCampaignsWithTheSameBuyer();
        Auth::logout();

        $response = $this->get(route('home'))->assertOk();

        $this->assertSame([
            'projects' => 2,
            'lots' => 5,
            'sold' => 2,
            'families' => 1,
            'square_meters' => 600,
        ], $response->viewData('stats'));
    }

    public function test_signed_in_user_sees_the_figures_of_every_campaign_not_only_the_current_one(): void
    {
        $this->seedTwoCampaignsWithTheSameBuyer();

        $response = $this->get(route('home'))->assertOk();

        $this->assertSame(5, $response->viewData('stats')['lots']);
    }

    public function test_projects_show_their_status_from_the_lots_left_and_upcoming_ones_go_last(): void
    {
        $upcoming = Category::create(['name' => 'Nueva']);
        $soldOut = Category::create(['name' => 'Vendida']);
        $fewLeft = Category::create(['name' => 'Pocas']);
        $onSale = Category::create(['name' => 'Abierta']);
        $this->user = User::factory()->create(['current_category_id' => $soldOut->id]);
        $this->actingAs($this->user);

        $this->createLots($soldOut, count: 2, sold: 2);
        $this->createLots($fewLeft, count: 4, sold: 3);
        $this->createLots($onSale, count: 4, sold: 2);
        Auth::logout();

        $projects = $this->get(route('home'))->assertOk()->viewData('projects');

        $this->assertSame(
            ['Vendida' => 'sold_out', 'Pocas' => 'few_left', 'Abierta' => 'on_sale', 'Nueva' => 'soon'],
            array_column($projects, 'status', 'name'),
        );
    }

    public function test_homepage_offers_the_account_statement_lookup_and_the_admin_login(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSeeLivewire(Lookup::class)
            ->assertSee('href="#estado-de-cuenta"', false)
            ->assertSee('href="'.route('login').'"', false);
    }

    /**
     * Two campaigns, 5 lots of 120 m², 2 sold, both to the same person (registered once per campaign).
     */
    private function seedTwoCampaignsWithTheSameBuyer(): void
    {
        $rivarca = Category::create(['name' => 'Rivarca']);
        $rivarcaII = Category::create(['name' => 'Rivarca II']);
        $this->user = User::factory()->create(['current_category_id' => $rivarca->id]);
        $this->actingAs($this->user);

        $this->createLots($rivarca, count: 3, sold: 1, document: '1.115.918.673');
        $this->createLots($rivarcaII, count: 2, sold: 1, document: '1115918673');
    }

    private function createLots(Category $category, int $count, int $sold, string $document = '52000111'): void
    {
        $this->user->switchCategory($category);
        $block = Block::create(['code' => "M-{$category->id}"]);

        $promise = null;
        if ($sold > 0) {
            $promise = Promise::create(['number' => "P-{$category->id}", 'signature_date' => now()]);
            $promise->buyers()->attach(Buyer::factory()->create(['document_number' => $document]));
        }

        foreach (range(1, $count) as $number) {
            Parcel::create([
                'number' => $number,
                'block_id' => $block->id,
                'area_m2' => 120,
                'promise_id' => $number <= $sold ? $promise->id : null,
            ]);
        }
    }
}
