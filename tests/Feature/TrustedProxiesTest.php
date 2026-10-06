<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

test('untrusted forwarded headers do not change generated URLs', function () {
    config(['trustedproxy.proxies' => []]);
    Route::get('/_tests/proxy', fn (Request $request) => response()->json(['url' => $request->getSchemeAndHttpHost()]));

    $this->withServerVariables(['REMOTE_ADDR' => '192.0.2.10'])
        ->getJson('http://localhost/_tests/proxy', ['X-Forwarded-Proto' => 'https', 'X-Forwarded-Host' => 'attacker.example'])
        ->assertJsonPath('url', 'http://localhost');
});

test('configured proxy supports HTTPS forwarded URLs', function () {
    config(['trustedproxy.proxies' => '192.0.2.10']);
    Route::get('/_tests/proxy', fn (Request $request) => response()->json([
        'url' => $request->getSchemeAndHttpHost(),
        'dashboard' => route('dashboard'),
    ]));

    $this->withServerVariables(['REMOTE_ADDR' => '192.0.2.10'])
        ->getJson('/_tests/proxy', ['X-Forwarded-Proto' => 'https', 'X-Forwarded-Host' => 'starter.example'])
        ->assertJsonPath('url', 'https://starter.example')
        ->assertJsonPath('dashboard', 'https://starter.example/dashboard');
});
