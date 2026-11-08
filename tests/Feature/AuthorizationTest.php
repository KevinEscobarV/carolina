<?php

namespace Tests\Feature;

use App\Livewire\Block\Index\Create as BlockCreate;
use App\Livewire\Block\Index\Table as BlockTable;
use App\Livewire\Buyer\Index\Create as BuyerCreate;
use App\Livewire\Buyer\Index\Table as BuyerTable;
use App\Livewire\Category\Index\Create as CategoryCreate;
use App\Livewire\Category\Index\Table as CategoryTable;
use App\Livewire\Deed\Index\Create as DeedCreate;
use App\Livewire\Deed\Index\Import as DeedImport;
use App\Livewire\Deed\Index\Table as DeedTable;
use App\Livewire\Notification\Index\SMS;
use App\Livewire\Parcel\Index\Import as ParcelImport;
use App\Livewire\Parcel\Index\ImportRegistrationNumber;
use App\Livewire\Parcel\Index\Table as ParcelTable;
use App\Livewire\Payment\Index\Table as PaymentTable;
use App\Livewire\Permission\Index\Create as RoleCreate;
use App\Livewire\Permission\Index\Table as RoleTable;
use App\Livewire\Promise\Index\Table as PromiseTable;
use App\Livewire\Reports\Buyer\General as BuyerReport;
use App\Livewire\Reports\Parcel\General as ParcelReport;
use App\Livewire\Reports\Payment\General as PaymentReport;
use App\Livewire\Reports\Promise\General as PromiseReport;
use App\Livewire\Reports\Promise\WalletStatus;
use App\Livewire\User\Index\Create as UserCreate;
use App\Livewire\User\Index\Table as UserTable;
use App\Models\Buyer;
use App\Models\Category;
use App\Models\User;
use Database\Seeders\Permissions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * A read-only user, like the GERENCIA role in production, must not change data.
 */
class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(Permissions::class);

        $category = Category::create(['name' => 'Campaña']);
        $this->user = User::factory()->create(['current_category_id' => $category->id]);
        $this->user->givePermissionTo([
            'view.buyers', 'view.payments', 'view.promises', 'view.blocks',
            'view.parcels', 'view.deeds', 'view.categories', 'view.users', 'view.roles',
        ]);

        $this->actingAs($this->user);
    }

    public static function readOnlyForbiddenActions(): array
    {
        return [
            'archive block' => [BlockTable::class, 'delete'],
            'archive buyer' => [BuyerTable::class, 'delete'],
            'archive category' => [CategoryTable::class, 'delete'],
            'archive deed' => [DeedTable::class, 'delete'],
            'archive parcel' => [ParcelTable::class, 'delete'],
            'archive payment' => [PaymentTable::class, 'delete'],
            'delete role' => [RoleTable::class, 'delete'],
            'archive promise' => [PromiseTable::class, 'delete'],
            'archive user' => [UserTable::class, 'delete'],
            'restore buyer' => [BuyerTable::class, 'restore'],
            'archive selected buyers' => [BuyerTable::class, 'deleteSelected'],
            'edit block' => [BlockTable::class, 'update'],
            'edit category' => [CategoryTable::class, 'update'],
            'edit role' => [RoleTable::class, 'update'],
            'edit user' => [UserTable::class, 'update'],
            'create block' => [BlockCreate::class, 'save'],
            'create buyer' => [BuyerCreate::class, 'save'],
            'create category' => [CategoryCreate::class, 'save'],
            'create deed' => [DeedCreate::class, 'save'],
            'create role' => [RoleCreate::class, 'save'],
            'create user' => [UserCreate::class, 'save'],
            'import deeds' => [DeedImport::class, 'import'],
            'import parcels' => [ParcelImport::class, 'import'],
            'import registration numbers' => [ImportRegistrationNumber::class, 'import'],
            'send personalized sms' => [SMS::class, 'sendSMS'],
        ];
    }

    #[DataProvider('readOnlyForbiddenActions')]
    public function test_read_only_user_cannot_run_the_action(string $component, string $action): void
    {
        $arguments = in_array($action, ['delete', 'restore'], true) ? [1] : [];

        Livewire::withoutLazyLoading()->test($component)
            ->call($action, ...$arguments)
            ->assertForbidden();
    }

    public function test_delete_permission_allows_archiving(): void
    {
        $this->user->givePermissionTo('delete.buyers');
        $buyer = Buyer::factory()->create();

        Livewire::withoutLazyLoading()->test(BuyerTable::class)
            ->call('delete', $buyer->id)
            ->assertOk();

        $this->assertSoftDeleted($buyer);
    }

    public static function reports(): array
    {
        return [
            'buyers' => [BuyerReport::class],
            'parcels' => [ParcelReport::class],
            'payments' => [PaymentReport::class],
            'promises' => [PromiseReport::class],
            'wallet status' => [WalletStatus::class],
        ];
    }

    #[DataProvider('reports')]
    public function test_reports_require_the_view_permission_of_their_data(string $report): void
    {
        $this->user->syncPermissions([]);

        Livewire::test($report)->call('exportGeneral')->assertForbidden();
    }

    public function test_the_reports_page_needs_access_to_some_report(): void
    {
        $this->get(route('reports'))->assertOk();

        $this->user->syncPermissions(['view.blocks']);

        $this->get(route('reports'))->assertForbidden();
    }

    public static function selectEndpoints(): array
    {
        return [
            'buyers' => ['api.buyers.index'],
            'promises' => ['api.promises.index'],
            'categories' => ['api.categories.index'],
            'blocks' => ['api.blocks.index'],
            'parcels for promises' => ['api.parcels.promises'],
            'parcels for deeds' => ['api.parcels.deeds'],
        ];
    }

    #[DataProvider('selectEndpoints')]
    public function test_select_endpoints_require_the_view_permission(string $route): void
    {
        $this->user->syncPermissions([]);

        $this->getJson(route($route))->assertForbidden();
    }
}
