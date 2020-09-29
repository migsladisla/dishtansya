<?php

namespace Tests\Unit;

use Faker\Factory;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    public $email;
    public $password;

    public function __construct()
    {
        parent::__construct();
        $this->email    = 'migsladisla@gmail.com';
        $this->password = 'password';
    }

    /** @test */
    public function user_can_register_successfully()
    {
        // Send register POST request
        $response = $this->json('POST', '/api/register', [
            'email'    => $this->email,
            'password' => $this->password,
        ]);

        // Check status code and JSON response
        $response
            ->assertStatus(201)
            ->assertExactJson(['message' => 'User successfully registered']);

        // Check if email exists
        $this->assertDatabaseHas('users', ['email' => $this->email]);
    }

    /** @test */
    public function user_email_is_taken()
    {
        // Send register POST request
        $response = $this->json('POST', '/api/register', [
            'email'    => $this->email,
            'password' => $this->password,
        ]);

        // Check status code and JSON response
        $response
            ->assertStatus(400)
            ->assertExactJson(['message' => 'Email already taken']);

        // Check if email exists
        $this->assertDatabaseHas('users', ['email' => $this->email]);
    }
}
