<?php

use App\Data\ErrorToastResponseData;
use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\ExceptionResponse;
use Inertia\Support\Header;
use Symfony\Component\HttpFoundation\Response;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(
            append: [
                HandleInertiaRequests::class,
                AddLinkHeadersForPreloadedAssets::class,
            ],
        );
        $middleware->trustProxies(
            headers: Request::HEADER_X_FORWARDED_FOR
            | Request::HEADER_X_FORWARDED_HOST
            | Request::HEADER_X_FORWARDED_PORT
            | Request::HEADER_X_FORWARDED_PROTO
        );
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
        $exceptions->respond(function (Response $response, Throwable $exception, Request $request) {
            $statusCode = $response->getStatusCode();
            if ($statusCode < 400 || (!$request->header(Header::INERTIA) && ($request->is('api/*') || $request->expectsJson()))) {
                return $response;
            }

            if ($statusCode === 419) {
                return Inertia::flash(
                    'warning_alert',
                    config('errors.statuses.419.detail', 'The page expired, please try again.'),
                )->back();
            }

            if ($statusCode >= 500 && app()->hasDebugModeEnabled()) {
                return $response;
            }

            $errorMetadata = config("errors.statuses.{$statusCode}")
                ?? config($statusCode >= 500 ? 'errors.defaults.5xx' : 'errors.defaults.4xx', []);
            $statusText = Response::$statusTexts[$statusCode] ?? 'Error';
            $errorDetail = $errorMetadata['detail'] ?? 'An unexpected error occurred.';
            $errorIcon = $errorMetadata['icon'] ?? 'i-lucide-alert-triangle';

            if ($request->header(Header::INERTIA) && !$request->isMethodSafe()) {
                return response()->json((new ErrorToastResponseData(
                    status: $statusCode,
                    errorSummary: "{$statusText} - {$statusCode}",
                    errorDetail: $errorDetail,
                    errorIcon: $errorIcon,
                ))->toArray(), $statusCode);
            }

            return app(ExceptionResponse::class, [
                'exception' => $exception,
                'request' => $request,
                'response' => $response,
            ])->render('Error', [
                'title' => $statusText,
                'detail' => $errorDetail,
                'status' => $statusCode,
                'homepageRoute' => route(name: 'index', absolute: false),
            ])->withSharedData()->toResponse($request);
        });
    })->create();
