<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserRegistrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test successful user registration
     */
    public function test_user_can_register_with_valid_data(): void
    {
        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(201)
                ->assertJson([
                    'success' => true,
                    'message' => 'User registered successfully',
                ])
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'user' => [
                            'id',
                            'name',
                            'email',
                            'created_at',
                        ]
                    ]
                ]);

        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);
    }

    /**
     * Test registration with invalid email
     */
    public function test_user_cannot_register_with_invalid_email(): void
    {
        $userData = [
            'name' => 'John Doe',
            'email' => 'invalid-email',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(422)
                ->assertJson([
                    'success' => false,
                    'message' => 'Validation failed',
                ])
                ->assertJsonValidationErrors(['email']);
    }

    /**
     * Test registration with duplicate email
     */
    public function test_user_cannot_register_with_duplicate_email(): void
    {
        // First registration
        $userData1 = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $this->postJson('/api/register', $userData1);

        // Second registration with same email
        $userData2 = [
            'name' => 'Jane Doe',
            'email' => 'john@example.com',
            'password' => 'password456',
            'password_confirmation' => 'password456',
        ];

        $response = $this->postJson('/api/register', $userData2);

        $response->assertStatus(422)
                ->assertJson([
                    'success' => false,
                    'message' => 'Validation failed',
                ])
                ->assertJsonValidationErrors(['email']);
    }

    /**
     * Test registration with short password
     */
    public function test_user_cannot_register_with_short_password(): void
    {
        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => '123',
            'password_confirmation' => '123',
        ];

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(422)
                ->assertJson([
                    'success' => false,
                    'message' => 'Validation failed',
                ])
                ->assertJsonValidationErrors(['password']);
    }

    /**
     * Test registration with mismatched password confirmation
     */
    public function test_user_cannot_register_with_mismatched_password_confirmation(): void
    {
        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password456',
        ];

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(422)
                ->assertJson([
                    'success' => false,
                    'message' => 'Validation failed',
                ])
                ->assertJsonValidationErrors(['password']);
    }

    /**
     * Test registration with missing required fields
     */
    public function test_user_cannot_register_with_missing_fields(): void
    {
        $response = $this->postJson('/api/register', []);

        $response->assertStatus(422)
                ->assertJson([
                    'success' => false,
                    'message' => 'Validation failed',
                ])
                ->assertJsonValidationErrors(['name', 'email', 'password']);
    }
} 