<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;

uses(RefreshDatabase::class);

test('successful customer login', function () {
    $user = User::factory()->create([
        'role' => 'customer',
        'password' => bcrypt('password123'),
    ]);

    $response = $this->postJson('/api/authenticate', [
        'email' => $user->email,
        'password' => 'password123',
    ]);

    $response->assertStatus(Response::HTTP_OK)
        ->assertJsonStructure(['token', 'user'])
        ->assertJsonPath('user.role', 'customer');
});

test('invalid customer credentials returns 401 unauthorized', function () {
    $user = User::factory()->create([
        'role' => 'customer',
        'password' => bcrypt('password123'),
    ]);

    $response = $this->postJson('/api/authenticate', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $response->assertStatus(Response::HTTP_UNAUTHORIZED)
        ->assertJsonStructure(['message']);
});

test('customer cannot login via admin portal', function () {
    $user = User::factory()->create([
        'role' => 'customer',
        'password' => bcrypt('password123'),
    ]);

    $response = $this->postJson('/api/admin/login', [
        'email' => $user->email,
        'password' => 'password123',
    ]);

    $response->assertStatus(Response::HTTP_FORBIDDEN)
        ->assertJsonMissing(['token']);
});

test('admin cannot login via customer portal', function () {
    $user = User::factory()->create([
        'role' => 'admin',
        'password' => bcrypt('password123'),
    ]);

    $response = $this->postJson('/api/authenticate', [
        'email' => $user->email,
        'password' => 'password123',
    ]);

    $response->assertStatus(Response::HTTP_FORBIDDEN)
        ->assertJsonMissing(['token']);
});

test('admin can login via admin portal', function () {
    $user = User::factory()->create([
        'role' => 'admin',
        'password' => bcrypt('password123'),
    ]);

    $response = $this->postJson('/api/admin/login', [
        'email' => $user->email,
        'password' => 'password123',
    ]);

    $response->assertStatus(Response::HTTP_OK)
        ->assertJsonStructure(['token', 'user'])
        ->assertJsonPath('user.role', 'admin');
});

test('login requires valid payload and returns 422 unprocessable entity', function () {
    $response = $this->postJson('/api/authenticate', [
        'email' => 'not-an-email',
    ]);

    $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJsonValidationErrors(['email', 'password']);
});
