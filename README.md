# Laravel Nuxt UI Starter Kit

[![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?logo=laravel&logoColor=white)](https://laravel.com)
[![Inertia](https://img.shields.io/badge/Inertia-v2-9553E9)](https://inertiajs.com)
[![Vue](https://img.shields.io/badge/Vue-3-42B883?logo=vue.js&logoColor=white)](https://vuejs.org)
[![Nuxt UI](https://img.shields.io/badge/Made%20with-Nuxt%20UI-00DC82?logo=nuxt&labelColor=020420)](https://ui.nuxt.com)

A production-ready Laravel starter kit built with **Inertia.js + Vue 3 + TypeScript + Nuxt UI**.

This Laravel starter is based on [laravel/vue-starter-kit](https://github.com/laravel/vue-starter-kit) and demonstrate how to use [Nuxt UI](https://ui.nuxt.com) in a [Laravel](https://laravel.com/) application using [Inertia.js](https://inertiajs.com/).

---

## Features

- Laravel 12 + PHP 8.4
- Inertia.js v2 (SSR + Vue SPA client)
- Vue 3 with TypeScript (`strict: true`)
- Nuxt UI v4 components and theming
- Ziggy route integration for client and SSR
- Vite dev/build pipeline with optional SSR build
- Laravel Fortify authentication flows:
  - login/logout
  - registration
  - forgot/password reset
  - email verification
  - two-factor authentication (confirm + confirm password)
- Built-in auth rate limiting (login + 2FA)
- Centralized, configurable error behavior (`config/errors.php`)
- Pest test suite with focused feature coverage

---

## UX Enhancements Included

### 1) Automatic Toast Error Handling (Inertia mutation requests)

Non-GET Inertia requests that fail return a structured JSON error payload, and the frontend automatically displays a Nuxt UI toast.

This gives users immediate feedback for failed form submits/actions without custom toast plumbing in every page.

### 2) Session Flash Messages

Redirect-based actions can set standard flash keys:

- `flash_success`
- `flash_info`
- `flash_warning`
- `flash_error`
- `flash_neutral`

These are shared via Inertia and rendered automatically by `FlashMessages.vue` in all layouts.

### 3) Friendly Error Pages + 419 Handling

- GET errors render a dedicated Inertia error page.
- 419 (expired page/session) redirects back with a warning flash message.
- Status-specific icon/detail/color can be configured in `config/errors.php`.

---

## Requirements

- PHP 8.4+
- Composer 2+
- Node.js 22+ (recommended)
- npm 10+
- SQLite/MySQL/PostgreSQL (tests default to in-memory SQLite)

---

## Quick Start

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
```

Start development (Laravel server, queue worker, logs, Vite in one command):

```bash
composer run dev
```

Open the app at the URL shown by `php artisan serve` (typically `http://127.0.0.1:8000`).

---

## Common Commands

### Development

```bash
composer run dev        # full local workflow
composer run dev:ssr    # full workflow with Inertia SSR server
npm run dev             # Vite only
```

### Build

```bash
npm run build
npm run build:ssr
```

### Code Quality

```bash
vendor/bin/pint --dirty # format changed PHP files
vendor/bin/pint         # format all PHP files
npm run lint            # eslint --fix
npm run typecheck       # vue-tsc --noEmit
```

### Tests (Pest)

```bash
php artisan test
php artisan test --compact
```

Run a single test file:

```bash
php artisan test --compact tests/Feature/Auth/AuthenticationTest.php
```

Run a single test by name:

```bash
php artisan test --compact --filter="users can authenticate using the login screen"
```

Run one test case in one file:

```bash
php artisan test --compact tests/Feature/Settings/ProfileUpdateTest.php --filter="profile information can be updated"
```

---

## Docker Swarm Deployment (Single VPS, Zero-Downtime Rollouts)

This repository includes a first-pass Swarm deployment setup inspired by the Server Side Up Spin deployment model:

- `docker-compose.swarm.yml` for Swarm service definitions and rolling updates
- `.github/workflows/swarm-deploy.yml` for CI build + deploy to Swarm
- `docker-swarm-deploy.sh` for manual deploys from the Swarm manager

### Required GitHub Secrets

- `SWARM_STACK_NAME`
- `SSH_DEPLOY_PRIVATE_KEY`
- `SSH_DEPLOY_USER`
- `SSH_REMOTE_HOSTNAME`
- `SSH_REMOTE_KNOWN_HOSTS`
- `PRODUCTION_ENV_FILE_BASE64` (base64-encoded production `.env`)

### Required Runtime Variables

The Swarm stack expects `IMAGE_REPOSITORY` and `IMAGE_TAG` to be available at deploy time.

### Notes

- This setup uses rolling updates (`start-first`) + health checks and automatic rollback on failure.
- Do not use `php artisan down` in this deployment flow.
- For Swarm + Traefik, `traefik_network` must exist as an external overlay network.

---

## How to Use Flash Messages

From controllers, redirect or flash with any supported key:

```php
// In your controller...

// Chained to session helper
session()->flash('flash_success', 'Success - Resource created!');

// Or with a redirect
try {
    // ...
} catch (Throwable $e) {
    report($e);
    return redirect()
        ->route('example-route')
        ->with('flash_error', 'Error - Update failed, we experienced an issue.');
}
```

Flash messages are auto-shared and auto-rendered in the UI.

---

## How to Trigger a Toast-Friendly Error

For mutation requests, throw an exception (or abort) and the global error handler will return toast-ready metadata.

You can also use the included `ErrorToastException` for custom messages:

```php
// In your controller...

use App\Exceptions\ErrorToastException;

try {
    // ...
} catch (ExceptionWarrantingCustomErrorDetails $e) {
    report($e);
    // Will trigger an "error" severity level toast message on the front-end
    throw new ErrorToastException('Specific error message...');
}
```

The frontend listens to Inertia router `invalid`/`exception` events and shows a toast automatically.
