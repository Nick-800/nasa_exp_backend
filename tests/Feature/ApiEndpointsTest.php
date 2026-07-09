<?php

use App\Models\User;
use App\Models\Journal;
use App\Models\Favorite;
use App\Enums\FavoriteType;

it('registers a user successfully', function () {
    $response = $this->postJson('/api/auth/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password123',
    ]);

    $response->assertStatus(201)
             ->assertJsonStructure(['access_token', 'user' => ['id', 'email']]);
});

it('logs in a user successfully', function () {
    $user = User::factory()->create([
        'email' => 'test2@example.com',
        'password' => bcrypt('password123'),
    ]);

    $response = $this->postJson('/api/auth/login', [
        'email' => 'test2@example.com',
        'password' => 'password123',
    ]);

    $response->assertStatus(200)
             ->assertJsonStructure(['access_token', 'user']);
});

it('can fetch public journals', function () {
    $user = User::factory()->create();
    Journal::create([
        'user_id' => $user->id,
        'title' => 'Public Journal',
        'content' => 'Public content',
        'is_public' => true,
    ]);

    $this->actingAs($user)
        ->getJson('/api/journals/public')
        ->assertStatus(200)
        ->assertJsonPath('data.0.title', 'Public Journal');
});

it('can add and delete a favorite', function () {
    $user = User::factory()->create();

    // Add
    $response = $this->actingAs($user)
        ->postJson('/api/favorites', [
            'type' => FavoriteType::APOD->value,
            'external_id' => '2026-07-01',
            'metadata' => ['title' => 'Cool APOD']
        ])
        ->assertStatus(201)
        ->assertJsonStructure(['data' => ['id', 'type', 'external_id']]);

    $favoriteId = $response->json('data.id');

    // Remove
    $this->actingAs($user)
        ->deleteJson("/api/favorites/{$favoriteId}")
        ->assertStatus(204);
});

it('can fetch the user profile', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->getJson('/api/user/profile')
        ->assertStatus(200)
        ->assertJsonStructure(['data' => ['id', 'email']]);
});

