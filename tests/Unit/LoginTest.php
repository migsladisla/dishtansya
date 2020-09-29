<?php

namespace Tests\Unit;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_login_successfully()
    {
        // Generate user
        $user = User::create([
            'name'     => 'Migs Ladisla',
            'email'    => 'migsladisla@gmail.com',
            'password' => Hash::make('password'),
        ]);

        // Send login POST request
        $response = $this->json('POST', '/api/login', [
            'email'    => 'migsladisla@gmail.com',
            'password' => 'password',
        ]);

        // Check status code
        $response
            ->assertStatus(201);
    }

    /** @test */
    public function user_invalid_login_credentials()
    {
        // Generate user
        $user = User::create([
            'name'     => 'Migs Ladisla',
            'email'    => 'migsladisla@gmail.com',
            'password' => Hash::make('password'),
        ]);

        // Send login POST request
        $response = $this->json('POST', '/api/login', [
            'email'    => 'migsladisla@gmail.com',
            'password' => 'passwords',
        ]);

        // Check status code and JSON response
        $response
            ->assertStatus(401)
            ->assertExactJson(['message' => 'Invalid credentials']);
    }
}
