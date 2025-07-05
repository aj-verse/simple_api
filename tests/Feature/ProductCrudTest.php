<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductCrudTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test getting all products
     */
    public function test_can_get_all_products(): void
    {
        // Create some test products
        Product::factory()->count(3)->create();

        $response = $this->getJson('/api/products');

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Products retrieved successfully',
                ])
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'data' => [
                            '*' => [
                                'id',
                                'name',
                                'description',
                                'price',
                                'slug',
                                'quantity',
                                'created_at',
                                'updated_at',
                            ]
                        ],
                        'current_page',
                        'per_page',
                        'total',
                    ]
                ]);
    }

    /**
     * Test creating a product with valid data
     */
    public function test_can_create_product_with_valid_data(): void
    {
        $productData = [
            'name' => 'Test Product',
            'description' => 'This is a test product description',
            'price' => 99.99,
            'quantity' => 10,
        ];

        $response = $this->postJson('/api/products', $productData);

        $response->assertStatus(201)
                ->assertJson([
                    'success' => true,
                    'message' => 'Product created successfully',
                ])
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'product' => [
                            'id',
                            'name',
                            'description',
                            'price',
                            'slug',
                            'quantity',
                            'created_at',
                            'updated_at',
                        ]
                    ]
                ]);

        $this->assertDatabaseHas('products', [
            'name' => 'Test Product',
            'description' => 'This is a test product description',
            'price' => 99.99,
            'quantity' => 10,
        ]);
    }

    /**
     * Test creating a product with custom slug
     */
    public function test_can_create_product_with_custom_slug(): void
    {
        $productData = [
            'name' => 'Test Product',
            'description' => 'This is a test product description',
            'price' => 99.99,
            'slug' => 'custom-test-slug',
            'quantity' => 10,
        ];

        $response = $this->postJson('/api/products', $productData);

        $response->assertStatus(201);

        $this->assertDatabaseHas('products', [
            'slug' => 'custom-test-slug',
        ]);
    }

    /**
     * Test creating a product with auto-generated slug
     */
    public function test_auto_generates_slug_when_not_provided(): void
    {
        $productData = [
            'name' => 'Test Product With Spaces',
            'description' => 'This is a test product description',
            'price' => 99.99,
            'quantity' => 10,
        ];

        $response = $this->postJson('/api/products', $productData);

        $response->assertStatus(201);

        $this->assertDatabaseHas('products', [
            'name' => 'Test Product With Spaces',
            'slug' => 'test-product-with-spaces',
        ]);
    }

    /**
     * Test creating a product with invalid data
     */
    public function test_cannot_create_product_with_invalid_data(): void
    {
        $productData = [
            'name' => '', // Invalid: empty name
            'price' => -10, // Invalid: negative price
            'quantity' => 'invalid', // Invalid: not integer
        ];

        $response = $this->postJson('/api/products', $productData);

        $response->assertStatus(422)
                ->assertJson([
                    'success' => false,
                    'message' => 'Validation failed',
                ])
                ->assertJsonValidationErrors(['name', 'price', 'quantity']);
    }

    /**
     * Test creating a product with duplicate slug
     */
    public function test_cannot_create_product_with_duplicate_slug(): void
    {
        // Create first product
        Product::factory()->create(['slug' => 'test-slug']);

        $productData = [
            'name' => 'Another Product',
            'price' => 50.00,
            'slug' => 'test-slug', // Duplicate slug
            'quantity' => 5,
        ];

        $response = $this->postJson('/api/products', $productData);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['slug']);
    }

    /**
     * Test getting a specific product
     */
    public function test_can_get_specific_product(): void
    {
        $product = Product::factory()->create(['slug' => 'test-product']);

        $response = $this->getJson('/api/products/test-product');

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Product retrieved successfully',
                    'data' => [
                        'product' => [
                            'id' => $product->id,
                            'name' => $product->name,
                            'slug' => 'test-product',
                        ]
                    ]
                ]);
    }

    /**
     * Test getting a non-existent product
     */
    public function test_cannot_get_nonexistent_product(): void
    {
        $response = $this->getJson('/api/products/nonexistent-product');

        $response->assertStatus(404)
                ->assertJson([
                    'success' => false,
                    'message' => 'Product not found',
                ]);
    }

    /**
     * Test updating a product by ID
     */
    public function test_can_update_product_by_id(): void
    {
        $product = Product::factory()->create(['slug' => 'test-product']);

        $updateData = [
            'name' => 'Updated Product Name',
            'price' => 150.00,
            'quantity' => 25,
        ];

        $response = $this->putJson("/api/products/{$product->id}", $updateData);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Product updated successfully',
                ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Updated Product Name',
            'price' => 150.00,
            'quantity' => 25,
        ]);
    }

    /**
     * Test updating a product with invalid data
     */
    public function test_cannot_update_product_with_invalid_data(): void
    {
        $product = Product::factory()->create(['slug' => 'test-product']);

        $updateData = [
            'price' => -50, // Invalid: negative price
            'quantity' => 'invalid', // Invalid: not integer
        ];

        $response = $this->putJson("/api/products/{$product->id}", $updateData);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['price', 'quantity']);
    }

    /**
     * Test updating a non-existent product by ID
     */
    public function test_cannot_update_nonexistent_product_by_id(): void
    {
        $updateData = [
            'name' => 'Updated Name',
        ];

        $response = $this->putJson('/api/products/999', $updateData);

        $response->assertStatus(404)
                ->assertJson([
                    'success' => false,
                    'message' => 'Product not found',
                ]);
    }

    /**
     * Test deleting a product by ID
     */
    public function test_can_delete_product_by_id(): void
    {
        $product = Product::factory()->create(['slug' => 'test-product']);

        $response = $this->deleteJson("/api/products/{$product->id}");

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Product deleted successfully',
                ]);

        $this->assertDatabaseMissing('products', [
            'id' => $product->id,
        ]);
    }

    /**
     * Test deleting a non-existent product by ID
     */
    public function test_cannot_delete_nonexistent_product_by_id(): void
    {
        $response = $this->deleteJson('/api/products/999');

        $response->assertStatus(404)
                ->assertJson([
                    'success' => false,
                    'message' => 'Product not found',
                ]);
    }
} 