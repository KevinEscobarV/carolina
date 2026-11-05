<?php

namespace Tests\Feature;

use App\Enums\PaymentFrequency;
use App\Enums\PromisePaymentMethod;
use App\Enums\PromiseStatus;
use App\Livewire\Buyer\Index\Table as BuyerTable;
use App\Livewire\Promise\Index\Table as PromiseTable;
use App\Models\Buyer;
use App\Models\Category;
use App\Models\Promise;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\DataProvider;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Tables show documents with dots, so people search with or without them.
 */
class DocumentSearchTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $category = Category::create(['name' => 'Campaña']);
        $user = User::factory()->create(['current_category_id' => $category->id]);
        $user->assignRole(Role::create(['name' => 'Administrador']));

        $this->actingAs($user);
    }

    public static function documentSearches(): array
    {
        return [
            'saved without dots, searched with dots' => ['1115918673', '1.115.918.673'],
            'saved with dots, searched without dots' => ['1.115.918.673', '1115918673'],
            'part of the document with dots' => ['1115918673', '918.673'],
        ];
    }

    #[DataProvider('documentSearches')]
    public function test_buyers_table_finds_the_document(string $saved, string $search): void
    {
        $buyer = Buyer::factory()->create(['document_number' => $saved]);
        Buyer::factory()->create(['document_number' => '52000111']);

        $found = Livewire::withoutLazyLoading()->test(BuyerTable::class)
            ->set('search', $search)
            ->viewData('buyers');

        $this->assertSame([$buyer->id], $found->pluck('id')->all());
    }

    public function test_promises_table_finds_a_promise_by_buyer_document_with_dots(): void
    {
        $promise = Promise::create([
            'number' => 'P-1',
            'signature_date' => now(),
            'status' => PromiseStatus::cases()[0],
            'payment_method' => PromisePaymentMethod::cases()[0],
            'payment_frequency' => PaymentFrequency::cases()[0],
        ]);
        $promise->buyers()->attach(Buyer::factory()->create(['document_number' => '1115918673']));

        $found = Livewire::withoutLazyLoading()->test(PromiseTable::class)
            ->set('search', '1.115.918.673')
            ->viewData('promises');

        $this->assertSame([$promise->id], $found->pluck('id')->all());
    }

    public function test_buyer_select_finds_the_document_with_dots(): void
    {
        $buyer = Buyer::factory()->create(['document_number' => '1115918673']);

        $this->getJson(route('api.buyers.index', ['search' => '1.115.918.673']))
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonPath('0.id', $buyer->id);
    }
}
