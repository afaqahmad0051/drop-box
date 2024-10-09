<?php

namespace Tests\Feature\Controller;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function testRegisterUserSuccessfully(): void
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => 'password123',
            'confirm_password' => 'password123',
        ];

        $response = $this->post('/api/register', $data);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => ['token', 'name'],
            'message',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'john.doe@example.com',
        ]);
    }

    public function testRegisterExistingUser(): void
    {
        User::factory()->create([
            'email' => 'existinguser@example.com',
        ]);

        $data = [
            'name' => 'Test User',
            'email' => 'existinguser@example.com',
            'password' => 'password123',
            'confirm_password' => 'password123',
        ];

        $response = $this->post('/api/register', $data);

        $response->assertStatus(409);
        $response->assertJson([
            'success' => false,
            'message' => 'Email already exists.',
        ]);
    }

    public function testRegisterUserWithInvalidData(): void
    {
        $data = [
            'name' => '',
            'email' => 'invalid-email',
            'password' => 'short',
            'confirm_password' => 'not_matching',
        ];

        $response = $this->postJson('/api/register', $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'name',
            'email',
            'password',
            'confirm_password',
        ]);
    }

    public function testLoginUserSuccessfully()
    {
        User::factory()->create([
            'email' => 'john@example.com',
            'password' => bcrypt('password123'),
        ]);

        $data = [
            'email' => 'john@example.com',
            'password' => 'password123',
        ];

        $response = $this->post('/api/login', $data);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => ['token', 'name'],
            'message',
        ]);
    }

    public function testLoginFailsWithInvalidCredentials(): void
    {
        User::factory()->create([
            'email' => 'john@example.com',
            'password' => bcrypt('password123'),
        ]);

        $data = [
            'email' => 'john@example.com',
            'password' => 'wrongpassword',
        ];

        $response = $this->post('/api/login', $data);

        $response->assertStatus(401);
        $response->assertJson([
            'success' => false,
            'message' => 'Unauthorized',
        ]);
    }

    public function testTokenRevokedOnSubsequentLogins(): void
    {
        $user = User::factory()->create([
            'email' => 'john@example.com',
            'password' => bcrypt('password123'),
        ]);

        $data = [
            'email' => 'john@example.com',
            'password' => 'password123',
        ];
        $this->post('/api/login', $data);

        $this->assertCount(1, $user->tokens);
        $this->post('/api/login', $data);

        $this->assertCount(1, $user->fresh()->tokens);
    }
}
