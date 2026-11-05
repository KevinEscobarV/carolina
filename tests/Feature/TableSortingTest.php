<?php

namespace Tests\Feature;

use App\Enums\PaymentFrequency;
use App\Enums\PromisePaymentMethod;
use App\Enums\PromiseStatus;
use App\Livewire\Block\Index\Table as BlockTable;
use App\Livewire\Buyer\Index\Table as BuyerTable;
use App\Livewire\Category\Index\Table as CategoryTable;
use App\Livewire\Deed\Index\Table as DeedTable;
use App\Livewire\Notification\Index\Overdue;
use App\Livewire\Parcel\Index\Table as ParcelTable;
use App\Livewire\Payment\Index\Table as PaymentTable;
use App\Livewire\Permission\Index\Table as PermissionTable;
use App\Livewire\Promise\Index\Table as PromiseTable;
use App\Livewire\User\Index\Table as UserTable;
use App\Models\Buyer;
use App\Models\Category;
use App\Models\Promise;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\DataProvider;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class TableSortingTest extends TestCase
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

    /**
     * Every column a table header can sort by.
     */
    public static function sortableColumns(): array
    {
        $tables = [
            BlockTable::class => ['id', 'code', 'category', 'updated_at'],
            BuyerTable::class => ['id', 'names', 'surnames', 'email', 'document_type', 'document_number', 'civil_status'],
            CategoryTable::class => ['id', 'name'],
            DeedTable::class => ['id', 'number', 'book', 'signature_date', 'value', 'status', 'observations'],
            ParcelTable::class => ['id', 'number', 'block', 'position', 'value', 'registration_number', 'promise_id', 'updated_at'],
            PaymentTable::class => ['id', 'bill_number', 'agreement_date', 'agreement_amount', 'payment_date', 'paid_amount', 'payment_method', 'bank', 'observations'],
            PermissionTable::class => ['id', 'name', 'updated_at'],
            PromiseTable::class => ['id', 'number', 'buyer', 'signature_date', 'signature_deed_date', 'value', 'initial_fee', 'quota_amount', 'interest_rate', 'payment_frequency', 'payment_method', 'status', 'observations'],
            UserTable::class => ['id', 'name', 'email', 'updated_at'],
            Overdue::class => ['number', 'buyer', 'value', 'initial_fee', 'quota_amount', 'interest_rate', 'payment_frequency', 'payment_method'],
        ];

        $cases = [];

        foreach ($tables as $table => $columns) {
            foreach ($columns as $column) {
                $cases[class_basename(str_replace('\\Index\\Table', '', $table)).' by '.$column] = [$table, $column];
            }
        }

        return $cases;
    }

    #[DataProvider('sortableColumns')]
    public function test_table_sorts_by_each_header_column(string $table, string $column): void
    {
        Livewire::withoutLazyLoading()->test($table)
            ->call('sortBy', $column)
            ->assertOk()
            ->assertSet('sortCol', $column);
    }

    public function test_sorting_promises_by_buyer_lists_each_promise_once(): void
    {
        $promise = Promise::create([
            'number' => 'P-1',
            'signature_date' => now(),
            'status' => PromiseStatus::cases()[0],
            'payment_method' => PromisePaymentMethod::cases()[0],
            'payment_frequency' => PaymentFrequency::cases()[0],
        ]);
        $promise->buyers()->attach(Buyer::factory()->count(2)->create());

        $component = Livewire::withoutLazyLoading()->test(PromiseTable::class)->call('sortBy', 'buyer');

        $this->assertSame([$promise->id], $component->viewData('promises')->pluck('id')->all());
    }

    public function test_columns_outside_the_list_are_ignored(): void
    {
        Livewire::withoutLazyLoading()->test(UserTable::class)
            ->call('sortBy', 'password')
            ->assertSet('sortCol', null);

        // An unknown column in the URL would break the query if it reached ORDER BY
        Livewire::withoutLazyLoading()->withQueryParams(['sortCol' => 'not_a_column'])->test(UserTable::class)
            ->assertOk();
    }

    public function test_page_size_only_accepts_the_offered_values(): void
    {
        Livewire::withoutLazyLoading()->test(BuyerTable::class)
            ->set('perPage', '100000')
            ->assertSet('perPage', '10')
            ->set('perPage', '50')
            ->assertSet('perPage', '50');
    }
}
