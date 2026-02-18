<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Laravel\Fortify\Contracts\TwoFactorAuthenticationProvider;
use Laravel\Fortify\Fortify;

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

test('two factor settings page reports setup in progress when not confirmed', function () {
    $secret = app(TwoFactorAuthenticationProvider::class)->generateSecretKey();
    $encrypter = Fortify::currentEncrypter();
    $user = User::factory()->create();

    $user->forceFill([
        'two_factor_secret' => $encrypter->encrypt($secret),
        'two_factor_recovery_codes' => $encrypter->encrypt(json_encode(['code-1', 'code-2'])),
        'two_factor_confirmed_at' => null,
    ])->save();

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
                ->where('isConfirming', true)
        );
});

test('two factor settings page reports enabled when confirmed', function () {
    $secret = app(TwoFactorAuthenticationProvider::class)->generateSecretKey();
    $encrypter = Fortify::currentEncrypter();
    $user = User::factory()->create();

    $user->forceFill([
        'two_factor_secret' => $encrypter->encrypt($secret),
        'two_factor_recovery_codes' => $encrypter->encrypt(json_encode(['code-1', 'code-2'])),
        'two_factor_confirmed_at' => now(),
    ])->save();

    $this->actingAs($user)
        ->withSession([
            'auth.password_confirmed_at' => time(),
        ])
        ->get('/settings/two-factor')
        ->assertOk()
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('settings/two-factor', false)
                ->where('twoFactorEnabled', true)
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
