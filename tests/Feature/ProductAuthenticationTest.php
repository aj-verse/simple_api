<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that unauthenticated users cannot access product endpoints
     */
    public function test_unauthenticated_users_cannot_access_products(): void
    {
        // Test GET /api/products
        $response = $this->getJson('/api/products');
        $response->assertStatus(401)
                ->assertJson([
                    'success' => false,
                    'message' => 'Unauthenticated. Please login to access this resource.',
                ]);

        // Test POST /api/products
        $response = $this->postJson('/api/products', []);
        $response->assertStatus(401)
                ->assertJson([
                    'success' => false,
                    'message' => 'Unauthenticated. Please login to access this resource.',
                ]);

        // Test GET /api/products/{slug}
        $response = $this->getJson('/api/products/test-product');
        $response->assertStatus(401)
                ->assertJson([
                    'success' => false,
                    'message' => 'Unauthenticated. Please login to access this resource.',
                ]);

        // Test PUT /api/products/{id}
        $response = $this->putJson('/api/products/1', []);
        $response->assertStatus(401)
                ->assertJson([
                    'success' => false,
                    'message' => 'Unauthenticated. Please login to access this resource.',
                ]);

        // Test DELETE /api/products/{id}
        $response = $this->deleteJson('/api/products/1');
        $response->assertStatus(401)
                ->assertJson([
                    'success' => false,
                    'message' => 'Unauthenticated. Please login to access this resource.',
                ]);
    }

    /**
     * Test that authenticated users can access product endpoints
     */
    public function test_authenticated_users_can_access_products(): void
    {
        // Create a user and get authentication token
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        // Test GET /api/products
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/products');

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Products retrieved successfully',
                ]);

        // Test POST /api/products
        $productData = [
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 99.99,
            'quantity' => 10,
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/products', $productData);

        $response->assertStatus(201)
                ->assertJson([
                    'success' => true,
                    'message' => 'Product created successfully',
                ]);

        // Get the created product
        $product = Product::first();

        // Test GET /api/products/{slug}
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson("/api/products/{$product->slug}");

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Product retrieved successfully',
                ]);

        // Test PUT /api/products/{id}
        $updateData = [
            'name' => 'Updated Product',
            'price' => 149.99,
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson("/api/products/{$product->id}", $updateData);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Product updated successfully',
                ]);

        // Test DELETE /api/products/{id}
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->deleteJson("/api/products/{$product->id}");

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Product deleted successfully',
                ]);
    }

    /**
     * Test that invalid tokens are rejected
     */
    public function test_invalid_tokens_are_rejected(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer invalid-token',
        ])->getJson('/api/products');

        $response->assertStatus(401)
                ->assertJson([
                    'success' => false,
                    'message' => 'Unauthenticated. Please login to access this resource.',
                ]);
    }

    /**
     * Test that missing Authorization header is rejected
     */
    public function test_missing_authorization_header_is_rejected(): void
    {
        $response = $this->getJson('/api/products');

        $response->assertStatus(401)
                ->assertJson([
                    'success' => false,
                    'message' => 'Unauthenticated. Please login to access this resource.',
                ]);
    }
} 