<?php

namespace Tests\Feature;

use App\Models\Block;
use App\Models\Category;
use App\Models\Parcel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * The parcels select of the promise form, filtered by the chosen block.
 */
class ParcelSelectTest extends TestCase
{
    use RefreshDatabase;

    private Block $chosenBlock;

    private Parcel $otherBlockParcel;

    protected function setUp(): void
    {
        parent::setUp();

        $category = Category::create(['name' => 'Campaña']);
        $user = User::factory()->create(['current_category_id' => $category->id]);
        $user->assignRole(Role::create(['name' => 'Administrador']));
        $this->actingAs($user);

        $this->chosenBlock = Block::create(['code' => 'ZM4 Y 5 - 4']);
        Parcel::create(['number' => 1, 'block_id' => $this->chosenBlock->id]);
        Parcel::create(['number' => 2, 'block_id' => $this->chosenBlock->id]);

        $otherBlock = Block::create(['code' => 'ZM1']);
        $this->otherBlockParcel = Parcel::create(['number' => 1, 'block_id' => $otherBlock->id]);
    }

    public function test_only_parcels_of_the_chosen_block_are_listed(): void
    {
        $this->getJson(route('api.parcels.promises', $this->chosenBlock))
            ->assertOk()
            ->assertJsonCount(2)
            ->assertJsonPath('0.block_id', $this->chosenBlock->id)
            ->assertJsonPath('1.block_id', $this->chosenBlock->id);
    }

    public function test_selected_parcels_of_another_block_keep_their_labels(): void
    {
        $this->getJson(route('api.parcels.promises', [
            'block' => $this->chosenBlock,
            'selected' => [$this->otherBlockParcel->id],
        ]))
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonPath('0.id', $this->otherBlockParcel->id);
    }
}
