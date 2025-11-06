<?php

namespace Tests\Feature;

use Database\Seeders\ProductSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProductTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function testProductSuccess()
    {
        $this->seed(UserSeeder::class);
        $this->seed(ProductSeeder::class);
        $this->get('/api/products',
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'description', 'stock', 'price']
                ]
            ])
            ->assertJsonFragment([
                'name' => 'Indomie',
                'description' => 'Makanan instan',
                'stock' => "10",
                'price' => "3000"
            ]);
    }
    public function testProductNotFound()
    {
        $this->seed(UserSeeder::class);
        $this->get('/api/products',
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(200)
            ->assertJson([
                'data' => []
            ]);
    }

    public function testAddProductSuccess()
    {
        $this->seed(UserSeeder::class);

        $data = [
            'name' => 'Teh Botol',
            'description' => 'Minuman segar',
            'stock' => 20,
            'price' => 5000,
        ];

        $this->post('/api/products/add', $data, [
            'Authorization' => 'test'
        ])
            ->assertStatus(201)
            ->assertJsonFragment([
                'message' => 'Product added successfully',
                'name' => 'Teh Botol'
            ]);

        $this->assertDatabaseHas('products', [
            'name' => 'Teh Botol'
        ]);
    }

    public function testAddProductValidationError()
    {
        $this->seed(UserSeeder::class);

        $data = [
            'name' => '',
            'stock' => -1,
            'price' => -1000,
        ];

        $this->post('/api/products/add', $data, [
            'Authorization' => 'test'
        ])
            ->assertStatus(302);
    }

    public function testUpdateProductSuccess()
    {
        $this->withoutMiddleware(); // hilangkan auth jika route butuh token

        $this->seed(UserSeeder::class);
        $this->seed(ProductSeeder::class);

        $update = [
            'name' => 'Indomie Goreng',
            'stock' => 50,
            'price' => 3500,
        ];

        $this->patch('/api/products/1', $update)
            ->assertStatus(200)
            ->assertJsonFragment([
                'message' => 'Product updated successfully',
                'name' => 'Indomie Goreng'
            ]);

        $this->assertDatabaseHas('products', [
            'id' => 1,
            'name' => 'Indomie Goreng'
        ]);
    }


    public function testUpdateProductNotFound()
    {
        $this->seed(UserSeeder::class);

        $update = [
            'name' => 'Produk Tidak Ada'
        ];

        $this->patch('/api/products/update/999', $update, [
            'Authorization' => 'test'
        ])
            ->assertStatus(404)
            ->assertJson([
                'message' => 'Product not found'
            ]);
    }

    public function testDeleteProductSuccess()
    {
        $this->seed(UserSeeder::class);
        $this->seed(ProductSeeder::class);

        $product = \App\Models\Product::first();

        $this->delete("/api/products/delete/{$product->id}", [], [
            'Authorization' => 'test'
        ])
            ->assertStatus(200)
            ->assertJson([
                'message' => 'Product deleted successfully'
            ]);

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function testDeleteProductNotFound()
    {
        $this->seed(UserSeeder::class);

        $this->delete('/api/products/delete/999', [], [
            'Authorization' => 'test'
        ])
            ->assertStatus(404)
            ->assertJson([
                'message' => 'Product not found'
            ]);
    }
}
