<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\Attributes\DataProvider;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Smoke test: every screen renders for an administrator.
 */
class PagesTest extends TestCase
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

    public static function pages(): array
    {
        return [
            'dashboard' => ['dashboard'],
            'buyers' => ['buyers'],
            'parcels' => ['parcels'],
            'parcels.create' => ['parcels.create'],
            'blocks' => ['blocks'],
            'payments' => ['payments'],
            'payments.create' => ['payments.create'],
            'promises' => ['promises'],
            'promises.create' => ['promises.create'],
            'categories' => ['categories'],
            'deeds' => ['deeds'],
            'notifications' => ['notifications'],
            'settings' => ['settings'],
            'users' => ['users'],
            'permissions' => ['permissions'],
            'api.buyers.index' => ['api.buyers.index'],
            'api.buyers.other-categories' => ['api.buyers.other-categories'],
            'api.promises.index' => ['api.promises.index'],
            'api.categories.index' => ['api.categories.index'],
            'api.blocks.index' => ['api.blocks.index'],
            'api.parcels.promises' => ['api.parcels.promises'],
            'api.parcels.deeds' => ['api.parcels.deeds'],
        ];
    }

    #[DataProvider('pages')]
    public function test_page_renders(string $route): void
    {
        $this->get(route($route))->assertOk();
    }

    public function test_public_statement_page_renders(): void
    {
        Auth::logout();

        $this->get(route('personal-account-statement'))->assertOk();
    }
}
