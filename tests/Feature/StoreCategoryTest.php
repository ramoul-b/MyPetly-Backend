<?php

namespace Tests\Feature;

use App\Models\Store;
use App\Models\StoreCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class StoreCategoryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Gate::before(fn ($user, $ability) => true);
    }

    public function test_routes_require_authentication(): void
    {
        $this->getJson('/api/v1/store/categories/my')->assertStatus(401);
        $this->getJson('/api/v1/store/categories/1')->assertStatus(401);
        $this->postJson('/api/v1/store/categories')->assertStatus(401);
        $this->putJson('/api/v1/store/categories/1')->assertStatus(401);
        $this->deleteJson('/api/v1/store/categories/1')->assertStatus(401);
    }

    public function test_listing_flat_and_tree(): void
    {
        $user = User::factory()->create();
        $store = Store::factory()->create(['user_id' => $user->id]);
        Sanctum::actingAs($user);

        $parent = StoreCategory::create([
            'store_id' => $store->id,
            'name' => ['en' => 'Parent'],
            'slug' => 'parent',
        ]);

        $child = StoreCategory::create([
            'store_id' => $store->id,
            'name' => ['en' => 'Child'],
            'slug' => 'child',
            'parent_id' => $parent->id,
        ]);

        $flatResponse = $this->getJson('/api/v1/store/categories/my')
            ->assertStatus(200);
        $this->assertCount(2, $flatResponse->json());

        $treeResponse = $this->getJson('/api/v1/store/categories/my?flat=false')
            ->assertStatus(200);
        $this->assertCount(1, $treeResponse->json());
        $this->assertEquals($child->id, $treeResponse->json()[0]['children'][0]['id']);
    }

    public function test_show_create_update_delete_category(): void
    {
        $user = User::factory()->create();
        $store = Store::factory()->create(['user_id' => $user->id]);
        Sanctum::actingAs($user);

        $createResponse = $this->postJson('/api/v1/store/categories', [
            'name' => ['en' => 'Food'],
        ]);

        $createResponse->assertStatus(201)->assertJsonPath('name', 'Food');
        $categoryId = $createResponse->json('id');

        $this->getJson("/api/v1/store/categories/{$categoryId}")
            ->assertStatus(200)
            ->assertJsonPath('id', $categoryId);

        $this->putJson("/api/v1/store/categories/{$categoryId}", [
            'name' => ['en' => 'Updated'],
        ])
            ->assertStatus(200)
            ->assertJsonPath('name', 'Updated');

        $this->deleteJson("/api/v1/store/categories/{$categoryId}")
            ->assertStatus(200);

        $this->assertDatabaseMissing('store_categories', ['id' => $categoryId]);
    }

    public function test_not_found_and_conflict_responses(): void
    {
        $user = User::factory()->create();
        $store = Store::factory()->create(['user_id' => $user->id]);
        Sanctum::actingAs($user);

        $parent = StoreCategory::create([
            'store_id' => $store->id,
            'name' => ['en' => 'Parent'],
            'slug' => 'parent',
        ]);

        StoreCategory::create([
            'store_id' => $store->id,
            'name' => ['en' => 'Child'],
            'slug' => 'child',
            'parent_id' => $parent->id,
        ]);

        $this->getJson('/api/v1/store/categories/999')->assertStatus(404);
        $this->putJson('/api/v1/store/categories/999', [
            'name' => ['en' => 'X'],
        ])->assertStatus(404);
        $this->deleteJson('/api/v1/store/categories/999')->assertStatus(404);

        $this->deleteJson("/api/v1/store/categories/{$parent->id}")
            ->assertStatus(409);
    }
}

