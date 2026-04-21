<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Fortify\Fortify;
use PragmaRX\Google2FA\Google2FA;

uses(RefreshDatabase::class);

test('user can enable two factor authentication', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->withSession([
            'auth.password_confirmed_at' => time(),
        ])
        ->from(route('two-factor.show', absolute: false))
        ->post(route('two-factor.enable', absolute: false));

    $response->assertSessionHasNoErrors()
        ->assertRedirect(route('two-factor.show', absolute: false))
        ->assertSessionHas('status', Fortify::TWO_FACTOR_AUTHENTICATION_ENABLED);

    $user->refresh();

    expect($user->two_factor_secret)->not->toBeNull();
    expect($user->two_factor_recovery_codes)->not->toBeNull();
    expect($user->two_factor_confirmed_at)->toBeNull();
});

test('user can confirm two factor authentication and receives success toast flash', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->withSession([
            'auth.password_confirmed_at' => time(),
        ])
        ->from(route('two-factor.show', absolute: false))
        ->post(route('two-factor.enable', absolute: false));

    $user->refresh();

    $otpCode = app(Google2FA::class)->getCurrentOtp(decrypt($user->two_factor_secret));

    $response = $this->actingAs($user)
        ->withSession([
            'auth.password_confirmed_at' => time(),
        ])
        ->from(route('two-factor.show', absolute: false))
        ->post(route('two-factor.confirm', absolute: false), [
            'code' => $otpCode,
        ]);

    $response->assertSessionHasNoErrors()
        ->assertRedirect(route('two-factor.show', absolute: false))
        ->assertSessionHas('status', Fortify::TWO_FACTOR_AUTHENTICATION_CONFIRMED)
        ->assertInertiaFlash('success_toast', 'Two-factor authentication has been setup for your account.');

    $user->refresh();

    expect($user->two_factor_confirmed_at)->not->toBeNull();
    expect($user->hasEnabledTwoFactorAuthentication())->toBeTrue();
});

test('user can disable two factor authentication', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->withSession([
            'auth.password_confirmed_at' => time(),
        ])
        ->from(route('two-factor.show', absolute: false))
        ->post(route('two-factor.enable', absolute: false));

    $response = $this->actingAs($user)
        ->withSession([
            'auth.password_confirmed_at' => time(),
        ])
        ->from(route('two-factor.show', absolute: false))
        ->delete(route('two-factor.disable', absolute: false));

    $response->assertSessionHasNoErrors()
        ->assertRedirect(route('two-factor.show', absolute: false))
        ->assertSessionHas('status', Fortify::TWO_FACTOR_AUTHENTICATION_DISABLED);

    $user->refresh();

    expect($user->two_factor_secret)->toBeNull();
    expect($user->two_factor_recovery_codes)->toBeNull();
    expect($user->two_factor_confirmed_at)->toBeNull();
});

test('user can regenerate two factor recovery codes', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->withSession([
            'auth.password_confirmed_at' => time(),
        ])
        ->from(route('two-factor.show', absolute: false))
        ->post(route('two-factor.enable', absolute: false));

    $initialRecoveryCodes = $user->fresh()->recoveryCodes();

    $response = $this->actingAs($user)
        ->withSession([
            'auth.password_confirmed_at' => time(),
        ])
        ->from(route('two-factor.show', absolute: false))
        ->post(route('two-factor.regenerate-recovery-codes', absolute: false));

    $response->assertSessionHasNoErrors()
        ->assertRedirect(route('two-factor.show', absolute: false))
        ->assertSessionHas('status', Fortify::RECOVERY_CODES_GENERATED);

    $user->refresh();

    expect($user->recoveryCodes())->not->toEqual($initialRecoveryCodes);
});

test('user can view two factor recovery codes endpoint response', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->withSession([
            'auth.password_confirmed_at' => time(),
        ])
        ->post(route('two-factor.enable', absolute: false));

    $response = $this->actingAs($user)
        ->withSession([
            'auth.password_confirmed_at' => time(),
        ])
        ->getJson(route('two-factor.recovery-codes', absolute: false));

    $response->assertOk()
        ->assertJsonCount(8);
});
