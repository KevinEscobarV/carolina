<?php

namespace Tests\Feature;

use App\Enums\CivilStatus;
use App\Enums\DocumentType;
use App\Livewire\Buyer\Index\Create;
use App\Livewire\Public\Buyer\Statement\Page as StatementPage;
use App\Models\Buyer;
use App\Models\Category;
use App\Models\Promise;
use App\Models\Scopes\CategoryScope;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class BuyerAcrossCategoriesTest extends TestCase
{
    use RefreshDatabase;

    private Category $campaignA;

    private Category $campaignB;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->campaignA = Category::create(['name' => 'Campaña A']);
        $this->campaignB = Category::create(['name' => 'Campaña B']);
        $this->user = User::factory()->create(['current_category_id' => $this->campaignA->id]);

        $this->actingAs($this->user);
    }

    public function test_a_buyer_can_be_registered_again_in_another_category(): void
    {
        $this->createBuyerIn($this->campaignA, ['document_number' => '1115918673', 'email' => 'ana@example.com']);
        $this->user->switchCategory($this->campaignB);

        $this->fillCreateForm(['document_number' => '1115918673', 'email' => 'ana@example.com'])
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame(2, Buyer::withoutGlobalScope(CategoryScope::class)->where('document_number', '1115918673')->count());
    }

    public function test_document_and_email_stay_unique_within_a_category(): void
    {
        $this->createBuyerIn($this->campaignA, ['document_number' => '1115918673', 'email' => 'ana@example.com']);

        $this->fillCreateForm(['document_number' => '1115918673', 'email' => 'ana@example.com'])
            ->call('save')
            ->assertHasErrors(['form.document_number' => 'unique', 'form.email' => 'unique']);
    }

    public function test_a_buyer_from_another_category_can_be_copied_into_the_form(): void
    {
        $this->allowChangingCategory();
        $source = $this->createBuyerIn($this->campaignA);
        $this->user->switchCategory($this->campaignB);

        Livewire::test(Create::class)
            ->assertSee('Copiar datos de otra campaña')
            ->set('copyFrom', $source->id)
            ->assertSet('form.names', $source->names)
            ->assertSet('form.document_number', $source->document_number)
            ->assertSet('form.phone_one', $source->phone_one)
            ->set('form.phone_one', '573000000000')
            ->call('save')
            ->assertHasNoErrors()
            ->assertSet('copyFrom', null);

        $this->assertDatabaseHas('buyers', [
            'category_id' => $this->campaignB->id,
            'document_number' => $source->document_number,
            'phone_one' => '573000000000',
        ]);
        $this->assertDatabaseHas('buyers', ['id' => $source->id, 'phone_one' => $source->phone_one]);
    }

    public function test_copying_requires_the_change_category_permission(): void
    {
        $source = $this->createBuyerIn($this->campaignA);
        $this->user->switchCategory($this->campaignB);

        Livewire::test(Create::class)
            ->assertDontSee('Copiar datos de otra campaña')
            ->set('copyFrom', $source->id)
            ->assertForbidden();

        $this->get(route('api.buyers.other-categories'))->assertForbidden();
    }

    public function test_copy_options_skip_buyers_already_registered_in_the_current_category(): void
    {
        $this->allowChangingCategory();
        $onlyInA = $this->createBuyerIn($this->campaignA, ['document_number' => '111']);
        $this->createBuyerIn($this->campaignA, ['document_number' => '222']);
        $this->createBuyerIn($this->campaignB, ['document_number' => '222']);

        $this->getJson(route('api.buyers.other-categories'))
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonPath('0.id', $onlyInA->id);
    }

    public function test_public_statement_lists_promises_from_every_category(): void
    {
        $buyerA = $this->createBuyerIn($this->campaignA, ['document_number' => '1115918673']);
        $promiseA = Promise::create(['number' => 'P-A', 'signature_date' => now()]);
        $promiseA->buyers()->attach($buyerA);

        $buyerB = $this->createBuyerIn($this->campaignB, ['document_number' => '1115918673']);
        $promiseB = Promise::create(['number' => 'P-B', 'signature_date' => now()]);
        $promiseB->buyers()->attach($buyerB);

        auth()->logout();

        Livewire::test(StatementPage::class)
            ->set('document_number', '1.115.918.673')
            ->call('explore')
            ->assertSee(route('statement-download', [$promiseA->id, $buyerA->id]))
            ->assertSee(route('statement-download', [$promiseB->id, $buyerB->id]));
    }

    public function test_public_statement_needs_the_whole_document(): void
    {
        $buyer = $this->createBuyerIn($this->campaignA, ['document_number' => '1115918673']);
        $promise = Promise::create(['number' => 'P-A', 'signature_date' => now()]);
        $promise->buyers()->attach($buyer);

        auth()->logout();

        Livewire::test(StatementPage::class)
            ->set('document_number', '1115918')
            ->call('explore')
            ->assertSet('promises', [])
            ->assertDontSee(route('statement-download', [$promise->id, $buyer->id]));
    }

    /**
     * Buyers always take the user's current category when created.
     */
    private function createBuyerIn(Category $category, array $attributes = []): Buyer
    {
        $this->user->switchCategory($category);

        return Buyer::factory()->create($attributes);
    }

    private function allowChangingCategory(): void
    {
        Permission::create(['name' => 'change.category']);
        $this->user->givePermissionTo('change.category');
    }

    private function fillCreateForm(array $attributes = []): Testable
    {
        $attributes = array_merge([
            'names' => 'Ana',
            'surnames' => 'Pérez',
            'email' => 'ana.perez@example.com',
            'document_type' => DocumentType::CC,
            'document_number' => '1000000001',
            'civil_status' => CivilStatus::SINGLE,
            'phone_one' => '573212028286',
            'address' => 'Carrera 34 # 12 - 34',
        ], $attributes);

        $component = Livewire::test(Create::class);

        foreach ($attributes as $field => $value) {
            $component->set("form.{$field}", $value);
        }

        return $component;
    }
}
