<?php

use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

test('login screen renders the inertia auth login page', function () {
    $this->get(route('login'))
        ->assertOk()
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('auth/login', false)
                ->where('canResetPassword', true)
                ->where('canRegister', true)
        );
});

test('registration screen renders the inertia auth register page', function () {
    $this->get(route('register'))
        ->assertOk()
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('auth/register', false)
        );
});

test('forgot password screen renders the inertia auth forgot password page', function () {
    $this->get(route('password.request'))
        ->assertOk()
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('auth/forgot-password', false)
        );
});

test('reset password screen renders the inertia auth reset password page', function () {
    $token = 'sample-token';
    $email = 'user@example.com';

    $this->get(route('password.reset', ['token' => $token, 'email' => $email]))
        ->assertOk()
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('auth/reset-password', false)
                ->where('token', $token)
                ->where('email', $email)
        );
});

test('email verification notice screen renders the inertia auth verify email page', function () {
    $user = User::factory()->unverified()->create();

    $this->actingAs($user)
        ->get(route('verification.notice'))
        ->assertOk()
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('auth/verify-email', false)
        );
});

test('confirm password screen renders the inertia auth confirm password page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('password.confirm'))
        ->assertOk()
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('auth/confirm-password', false)
        );
});

test('two factor challenge screen renders the inertia auth two factor challenge page', function () {
    $user = User::factory()->create();

    $this->withSession(['login.id' => $user->id])
        ->get(route('two-factor.login'))
        ->assertOk()
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('auth/two-factor-challenge', false)
        );
});
