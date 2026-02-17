<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);

test('guests are redirected from settings pages', function () {
    $this->get('/settings')->assertRedirect('/login');
    $this->get('/settings/profile')->assertRedirect('/login');
    $this->get('/settings/password')->assertRedirect('/login');
    $this->get('/settings/two-factor')->assertRedirect('/login');
    $this->get('/settings/appearance')->assertRedirect('/login');
});

test('settings index redirects to profile settings page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/settings')
        ->assertRedirect('/settings/profile');
});

test('profile settings page renders', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/settings/profile')
        ->assertOk()
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('settings/profile', false)
                ->where('mustVerifyEmail', true)
        );
});

test('password settings page renders', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/settings/password')
        ->assertOk()
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('settings/password', false)
        );
});

test('two factor settings page renders', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->withSession([
            'auth.password_confirmed_at' => time(),
        ])
        ->get('/settings/two-factor')
        ->assertOk()
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('settings/two-factor', false)
                ->where('twoFactorEnabled', false)
                ->where('isConfirming', false)
        );
});

test('appearance settings page renders', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/settings/appearance')
        ->assertOk()
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('settings/appearance', false)
        );
});
