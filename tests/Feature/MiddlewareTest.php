<?php

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Sanctum\Sanctum;

uses(DatabaseTransactions::class);

test('admin routes block unauthenticated users', function () {
    $response = $this->getJson('/api/categories');
    $response->assertStatus(401);
});

test('admin routes block customers', function () {
    $user = User::factory()->create(['role' => 'customer']);
    Sanctum::actingAs($user);

    $response = $this->getJson('/api/categories');
    $response->assertStatus(401);
    $response->assertJson(['message' => 'Access Denied.']);
});

test('admin routes allow admins', function () {
    $user = User::factory()->create(['role' => 'admin']);
    Sanctum::actingAs($user);

    $response = $this->getJson('/api/categories');
    $response->assertStatus(200);
});

test('user routes block admins', function () {
    $user = User::factory()->create(['role' => 'admin']);
    Sanctum::actingAs($user);

    $response = $this->postJson('/api/save-order');
    $response->assertStatus(401);
    $response->assertJson(['message' => 'Access Denied.']);
});
