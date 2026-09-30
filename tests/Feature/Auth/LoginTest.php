<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

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

    $response->assertStatus(200)
        ->assertJsonStructure(['token', 'user'])
        ->assertJsonPath('user.role', 'customer');
});

test('invalid credentials rejection', function () {
    $user = User::factory()->create([
        'role' => 'customer',
        'password' => bcrypt('password123'),
    ]);

    $response = $this->postJson('/api/authenticate', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $response->assertStatus(401)
        ->assertJsonStructure(['message']);
});

test('customer cannot login via customer portal', function () {
    $user = User::factory()->create([
        'role' => 'customer',
        'password' => bcrypt('password123'),
    ]);

    $response = $this->postJson('/api/admin/login', [
        'email' => $user->email,
        'password' => 'password123',
    ]);

    $response->assertStatus(403)->assertJsonMissing(['token']);
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

    $response->assertStatus(200)->assertJsonStructure(['token', 'user'])->assertJsonPath('user.role', 'admin');
});
