<?php

use Inertia\Testing\AssertableInertia as Assert;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

test('inertia mutation requests receive configured error payload for known statuses', function () {
    $response = $this->post('/missing-endpoint', [], [
        'X-Inertia' => 'true',
    ]);

    $response->assertNotFound()
        ->assertJsonPath('status', 404)
        ->assertJsonPath('error_title', Response::$statusTexts[404])
        ->assertJsonPath('error_summary', Response::$statusTexts[404] . ' - 404')
        ->assertJsonPath('error_detail', config('errors.statuses.404.detail'))
        ->assertJsonPath('error_icon', config('errors.statuses.404.icon'))
        ->assertJsonPath('error_color', config('errors.statuses.404.color'));
});

test('inertia mutation requests use fallback metadata for unknown statuses', function () {
    Route::post('/_tests/error-418', fn () => abort(418));

    $response = $this->post('/_tests/error-418', [], [
        'X-Inertia' => 'true',
    ]);

    $response->assertStatus(418)
        ->assertJsonPath('status', 418)
        ->assertJsonPath('error_title', Response::$statusTexts[418])
        ->assertJsonPath('error_summary', Response::$statusTexts[418] . ' - 418')
        ->assertJsonPath('error_detail', config('errors.defaults.4xx.detail'))
        ->assertJsonPath('error_icon', config('errors.defaults.4xx.icon'))
        ->assertJsonPath('error_color', config('errors.defaults.4xx.color'));
});

test('session expired errors redirect back with configured flash message', function () {
    Route::post('/_tests/error-419', fn () => abort(419));

    $response = $this->from(route('index', absolute: false))->post('/_tests/error-419');

    $response->assertRedirect(route('index', absolute: false))
        ->assertSessionHas('flash_warning', config('errors.statuses.419.detail'));
});

test('error page receives resolved error metadata for get requests', function () {
    $response = $this->get('/missing-page');

    $response->assertNotFound()
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('Error', false)
                ->where('title', Response::$statusTexts[404])
                ->where('detail', config('errors.statuses.404.detail'))
                ->where('status', 404)
        );
});
