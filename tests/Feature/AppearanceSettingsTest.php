<?php

use App\Models\User;

test('appearance defaults to system for new users', function () {
    $user = User::factory()->create();

    expect($user->fresh()->appearance)->toBe('system');
});

test('user appearance can be updated to dark', function () {
    $user = User::factory()->create(['appearance' => 'system']);

    $user->update(['appearance' => 'dark']);

    expect($user->fresh()->appearance)->toBe('dark');
});

test('user appearance can be updated to light', function () {
    $user = User::factory()->create(['appearance' => 'dark']);

    $user->update(['appearance' => 'light']);

    expect($user->fresh()->appearance)->toBe('light');
});

test('head partial seeds localStorage script with user appearance on dashboard', function () {
    $user = User::factory()->create(['appearance' => 'dark']);

    $this->actingAs($user)
        ->get('/dashboard')
        ->assertSee('flux.appearance', false)
        ->assertSee('"dark"', false);
});

test('head partial removes localStorage entry for system appearance', function () {
    $user = User::factory()->create(['appearance' => 'system']);

    $this->actingAs($user)
        ->get('/dashboard')
        ->assertSee('localStorage.removeItem', false);
});
