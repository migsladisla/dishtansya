<?php

namespace Tests\Unit;

use Tests\TestCase;
use ProductSeeder;
use App\Models\Order;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_order_successfully()
    {
        // Seed products
        $this->seed(ProductSeeder::class);

        // Generate user
        $user = User::factory()->create();

        // Generate JWT token
        $token = JWTAuth::fromUser($user);

        // Set header token
        $this->withHeader('Authorization', "Bearer {$token}");

        // Send order POST request
        $response = $this->json('POST', '/api/order', [
            'product_id' => 5,
            'quantity'   => 3,
        ]);

        // Check status code and JSON response
        $response
            ->assertStatus(201)
            ->assertExactJson(['message' => 'You have successfully ordered this product']);
    }

    /** @test */
    public function user_order_failed_due_to_unavailability_of_stock()
    {
        // Seed products
        $this->seed(ProductSeeder::class);

        // Generate user
        $user = User::factory()->create();

        // Generate JWT token
        $token = JWTAuth::fromUser($user);

        // Set header token
        $this->withHeader('Authorization', "Bearer {$token}");

        // Send order POST request
        $response = $this->json('POST', '/api/order', [
            'product_id' => 1,
            'quantity'   => 6,
        ]);

        // Check status code and JSON response
        $response
            ->assertStatus(400)
            ->assertExactJson(['message' => 'Failed to order this product due to unavailability of the stock']);
    }
}
