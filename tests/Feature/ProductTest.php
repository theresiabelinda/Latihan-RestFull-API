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
}
