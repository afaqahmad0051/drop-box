<?php

namespace Tests\Feature\Controllers;

use Tests\TestCase;
use App\Models\User;
use App\Models\Collection;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CollectionControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function testGetCollections(): void
    {
        $user = User::factory()->create();
        Collection::factory()->count(5)->for($user)->create();

        $response = $this->actingAs($user)->get('/api/collections');
        $response->assertStatus(200);
        $response->assertJson([
            'error' => false,
            'message' => 'Collections retrieved successfully.',
        ]);
    }

    public function testCreateCollection(): void
    {
        $user = User::factory()->create();
        $requestData = [
            'name' => $this->faker->word,
            'description' => $this->faker->sentence,
        ];

        $response = $this->actingAs($user)->post('/api/collections', $requestData);
        $response->assertStatus(200);
        $response->assertJson([
            'error' => false,
            'message' => 'Collection created successfully.',
        ]);

        $this->assertDatabaseHas('collections', [
            'name' => $requestData['name'],
            'description' => $requestData['description'],
            'user_id' => $user->id,
        ]);
    }

    public function testShowSingleCollection(): void
    {
        $user = User::factory()->create();
        $collection = Collection::factory()->for($user)->create();

        $response = $this->actingAs($user)->get("/api/collections/{$collection->id}");
        $response->assertStatus(200);
        $response->assertJson([
            'error' => false,
            'message' => 'Collection retrieved successfully.',
            'data' => [
                'id' => $collection->id,
                'name' => $collection->name,
                'description' => $collection->description,
            ],
        ]);
    }

    public function testUpdateCollection(): void
    {
        $user = User::factory()->create();
        $collection = Collection::factory()->for($user)->create();
        $updatedData = [
            'name' => 'Updated Name',
            'description' => 'Updated Description',
        ];

        $response = $this->actingAs($user)->put("/api/collections/{$collection->id}", $updatedData);
        $response->assertStatus(200);
        $response->assertJson([
            'error' => false,
            'message' => 'Collection updated successfully.',
        ]);

        $this->assertDatabaseHas('collections', [
            'id' => $collection->id,
            'name' => $updatedData['name'],
            'description' => $updatedData['description'],
        ]);
    }

    public function testDeleteCollection(): void
    {
        $user = User::factory()->create();
        $collection = Collection::factory()->for($user)->create();

        $response = $this->actingAs($user)->delete("/api/collections/{$collection->id}");
        $response->assertStatus(200);
        $response->assertJson([
            'error' => false,
            'message' => 'Collection deleted successfully.',
        ]);

        $this->assertDatabaseMissing('collections', [
            'id' => $collection->id,
        ]);
    }
}
