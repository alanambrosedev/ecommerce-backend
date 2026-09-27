<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

it('registers a user successfully with role customer and hashed password', function () {
    $response = $this->postJson('/api/register', [
        'name' => 'John Doe',
        'email' => 'john@example.net',
        'password' => 'password@123',
    ]);

    $response->assertStatus(201);

    $this->assertDatabaseHas('users', [
        'email' => 'john@example.net',
        'role' => 'customer',
    ]);

    $user = User::where('email', 'john@example.net')->first();
    expect($user->password)->not->toBe('password@123');
    expect(Hash::check('password@123', $user->password))->toBeTrue();
});

it('rejects registration with a duplicate email', function () {
    User::factory()->create([
        'email' => 'test@example.com',
    ]);

    $response = $this->postJson('/api/register', [
        'name' => 'Jane Doe',
        'email' => 'test@example.com',
        'password' => 'password123',
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['email']);
});

it('rejects registration with a short password', function () {
    $response = $this->postJson('/api/register', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => '12345',
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['password']);
});
