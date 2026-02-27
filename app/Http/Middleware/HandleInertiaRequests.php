<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'ziggy' => [
                ...(new Ziggy())->toArray(),
                'location' => $request->url(),
            ],
            'name' => config('app.name'),
            'auth' => [
                'user' => fn () => $request->user()?->only(['id', 'name', 'email', 'email_verified_at', 'created_at', 'updated_at']),
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('flash_success'),
                'info' => fn () => $request->session()->get('flash_info'),
                'warning' => fn () => $request->session()->get('flash_warning'),
                'error' => fn () => $request->session()->get('flash_error'),
                'neutral' => fn () => $request->session()->get('flash_neutral'),
            ],
            'queryParams' => Inertia::always($request->query()),
        ];
    }
}
