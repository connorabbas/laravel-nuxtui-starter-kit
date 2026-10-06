<?php

use Inertia\Testing\AssertableInertia as Assert;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Exceptions;

test('inertia mutation requests receive configured error payload for known statuses', function () {
    $response = $this->post('/missing-endpoint', [], [
        'X-Inertia' => 'true',
    ]);

    $response->assertNotFound()
        ->assertJsonPath('status', 404)
        ->assertJsonPath('errorSummary', Response::$statusTexts[404] . ' - 404')
        ->assertJsonPath('errorDetail', config('errors.statuses.404.detail'))
        ->assertJsonPath('errorIcon', config('errors.statuses.404.icon'));
});

test('inertia mutation requests use fallback metadata for unknown statuses', function () {
    Route::post('/_tests/error-418', fn () => abort(418));

    $response = $this->post('/_tests/error-418', [], [
        'X-Inertia' => 'true',
    ]);

    $response->assertStatus(418)
        ->assertJsonPath('status', 418)
        ->assertJsonPath('errorSummary', Response::$statusTexts[418] . ' - 418')
        ->assertJsonPath('errorDetail', config('errors.defaults.4xx.detail'))
        ->assertJsonPath('errorIcon', config('errors.defaults.4xx.icon'));
});

test('session expired errors redirect back with configured flash message', function () {
    Route::post('/_tests/error-419', fn () => abort(419));

    $response = $this->from(route('index', absolute: false))->post('/_tests/error-419');

    $response->assertRedirect(route('index', absolute: false))
        ->assertInertiaFlash('warning_alert', config('errors.statuses.419.detail'));
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
                ->where('auth.user', null)
                ->where('config.appName', config('app.name'))
        );
});

test('ordinary JSON errors preserve Laravel responses', function (int $status) {
    Route::post('/_tests/json-error', fn () => abort($status, 'Original error'));

    $this->postJson('/_tests/json-error')
        ->assertStatus($status)
        ->assertJsonPath('message', 'Original error')
        ->assertJsonMissingPath('errorSummary')
        ->assertHeaderMissing('Location');
})->with([404, 419]);

test('API missing routes return JSON without an accept header', function () {
    $this->get('/api/missing')
        ->assertNotFound()
        ->assertJsonStructure(['message'])
        ->assertJsonMissingPath('component');
});

test('JSON validation errors retain field errors and status 422', function () {
    Route::post('/_tests/validation', function (Illuminate\Http\Request $request) {
        $request->validate(['name' => ['required']]);
    });

    $this->postJson('/_tests/validation')
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['name'])
        ->assertJsonMissingPath('errorSummary');
});

test('Inertia GET errors render a shared error page rather than a toast', function () {
    $this->get('/missing-page', ['X-Inertia' => 'true'])
        ->assertNotFound()
        ->assertHeader('X-Inertia', 'true')
        ->assertJsonPath('component', 'Error')
        ->assertJsonPath('props.status', 404)
        ->assertJsonPath('props.config.appName', config('app.name'));
});

test('throttled inertia mutations receive configured error feedback', function () {
    Route::post('/_tests/throttled', fn () => abort(429));

    $this->post('/_tests/throttled', [], ['X-Inertia' => 'true'])
        ->assertTooManyRequests()
        ->assertJsonPath('status', 429)
        ->assertJsonPath('errorDetail', config('errors.statuses.429.detail'));
});

test('debug 500 errors preserve exception diagnostics', function () {
    config(['app.debug' => true]);
    Exceptions::fake();
    Route::post('/_tests/debug-error', fn () => throw new RuntimeException('Debug diagnostic'));

    $this->postJson('/_tests/debug-error', [], ['X-Inertia' => 'true'])
        ->assertInternalServerError()
        ->assertJsonPath('message', 'Debug diagnostic')
        ->assertJsonMissingPath('errorSummary');
});

test('unknown server statuses use server metadata', function () {
    config(['app.debug' => false]);
    Route::post('/_tests/server-error', fn () => abort(599));

    $this->post('/_tests/server-error', [], ['X-Inertia' => 'true'])
        ->assertStatus(599)
        ->assertJsonPath('errorSummary', 'Error - 599')
        ->assertJsonPath('errorDetail', config('errors.defaults.5xx.detail'));
});

test('browser debug 500 errors keep the diagnostic page', function () {
    config(['app.debug' => true]);
    Exceptions::fake();
    Route::get('/_tests/browser-debug', fn () => throw new RuntimeException('Browser debug diagnostic'));

    $this->get('/_tests/browser-debug')
        ->assertInternalServerError()
        ->assertSee('Browser debug diagnostic')
        ->assertHeaderMissing('X-Inertia');
});

test('Inertia HEAD errors use navigation responses rather than mutation toasts', function () {
    $this->call('HEAD', '/missing-page', server: ['HTTP_X_INERTIA' => 'true'])
        ->assertNotFound()
        ->assertHeader('X-Inertia', 'true');
});
