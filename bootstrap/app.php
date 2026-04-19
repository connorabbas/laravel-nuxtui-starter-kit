<?php

use App\Data\ErrorToastResponseData;
use App\Exceptions\ErrorToastException;
use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Illuminate\Http\Request;
use Inertia\Inertia;
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
        // TrustProxies middleware for Traefik proxy handling assets over https
        // TODO: update `at` to actual Traefik subnet/container ip value, ideally via env/config entry
        $middleware->trustProxies(
            at: '*',
            headers: Request::HEADER_X_FORWARDED_FOR
            | Request::HEADER_X_FORWARDED_HOST
            | Request::HEADER_X_FORWARDED_PORT
            | Request::HEADER_X_FORWARDED_PROTO
        );
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->respond(function (Response $response, Throwable $exception, Request $request) {
            $statusCode = $response->getStatusCode();
            $errorStatuses = config('errors.statuses', []);
            $clientErrorDefaults = config('errors.defaults.4xx', []);
            $serverErrorDefaults = config('errors.defaults.5xx', []);

            $resolveErrorMetadata = function (int $status) use ($errorStatuses, $clientErrorDefaults, $serverErrorDefaults): array {
                if (isset($errorStatuses[$status]) && is_array($errorStatuses[$status])) {
                    return $errorStatuses[$status];
                }

                if ($status >= 500) {
                    return $serverErrorDefaults;
                }

                if ($status >= 400) {
                    return $clientErrorDefaults;
                }

                return [];
            };

            if ($statusCode === 419) {
                $errorMetadata = $resolveErrorMetadata($statusCode);

                return back()->with([
                    'flash_warning' => $errorMetadata['detail'] ?? 'The page expired, please try again.',
                ]);
            }

            if ($statusCode >= 400) {
                $errorMetadata = $resolveErrorMetadata($statusCode);
                $statusText = Response::$statusTexts[$statusCode] ?? 'Error';
                $errorDetail = $errorMetadata['detail'] ?? 'An unexpected error occurred.';
                $errorIcon = $errorMetadata['icon'] ?? 'i-lucide-alert-triangle';

                // Show exception modal in debug mode
                if (
                    $statusCode >= 500
                    && app()->hasDebugModeEnabled()
                    && !($exception instanceof ErrorToastException)
                ) {
                    return $response;
                }

                // Return JSON response for mutation requests to support toast handling
                if ($request->inertia() && !$request->isMethod('GET')) {
                    $errorSummary = "{$statusText} - {$statusCode}";

                    if ($exception instanceof ErrorToastException) {
                        $errorSummary = 'Error';
                        $errorDetail = $exception->getMessage();
                    }

                    $toastPayload = new ErrorToastResponseData(
                        status: $statusCode,
                        errorSummary: $errorSummary,
                        errorDetail: $errorDetail,
                        errorIcon: $errorIcon,
                    );

                    return response()->json($toastPayload->toArray(), $statusCode);
                }

                // Standard error page
                return Inertia::render('Error', [
                    'title' => $statusText,
                    'detail' => $errorDetail,
                    'status' => $statusCode,
                    'homepageRoute' => route(name: 'index', absolute: false),
                ])
                    ->toResponse($request)
                    ->setStatusCode($statusCode);
            }

            return $response;
        });
    })->create();
