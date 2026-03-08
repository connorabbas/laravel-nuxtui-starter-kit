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

## Docker Swarm Deployment (Single VPS, Zero to Hero)

This project deploys with Docker Swarm using immutable images from GHCR.

Deployment is CI/CD-only:

- Stack rollout is handled by `serversideup/github-action-docker-swarm-deploy`
- Laravel-specific post-deploy commands run in a dedicated CI SSH step
- No manual SSH deployment process is required

Deployment artifacts in this repo:

- `docker-compose-swarm.yml` (Swarm service definition)
- `.github/workflows/swarm-deploy.yml` (verify, build, deploy)

### Deployment model (Option 1)

- CI builds and pushes `ghcr.io/<owner>/<repo>:<git-sha>`
- CI deploys the stack through the Server Side Up Swarm deploy action
- CI then runs post-deploy Laravel commands over SSH
- The server does not need a checked-out app repository for automated deployments

### 1) VPS prerequisites

Assumptions:

- Docker Engine is installed
- Your DNS `A` record for the app domain points to the VPS
- Ports `22`, `80`, and `443` are open

Initialize Swarm (once):

```bash
docker swarm init
```

Create Traefik overlay network (once):

```bash
docker network create --driver overlay --attachable traefik_proxy
```

### 2) Deploy Traefik (once)

Use this stack as your base: `https://github.com/connorabbas/traefik-docker-compose/blob/master/docker-compose-swarm.yml`

On the VPS:

```bash
# Example
export LETSENCRYPT_EMAIL="you@example.com"
docker stack deploy -c docker-compose-swarm.yml traefik
```

Verify:

```bash
docker service ls
docker service ps traefik_traefik
```

### 3) Create deploy user (recommended)

For this CI/CD flow, GitHub Actions is the SSH client.

- The private key is stored in GitHub secret: `SSH_DEPLOY_PRIVATE_KEY`
- The matching public key is added to the VPS user: `~/.ssh/authorized_keys`

You can generate the keypair anywhere, but generating on your local machine is recommended so the private key is never created on the VPS.

On the VPS:

```bash
sudo adduser --disabled-password --gecos "" deploy
sudo usermod -aG docker deploy
sudo mkdir -p /home/deploy/.ssh
sudo chmod 700 /home/deploy/.ssh
sudo chown -R deploy:deploy /home/deploy/.ssh
```

From your local machine, generate keypair:

```bash
ssh-keygen -o -a 100 -t ed25519 -f ~/.ssh/id_ed25519_swarm_deploy -C deploy
```

Add public key to server:

```bash
cat ~/.ssh/id_ed25519_swarm_deploy.pub | ssh root@<server-ip> "cat >> /home/deploy/.ssh/authorized_keys"
ssh root@<server-ip> "chown deploy:deploy /home/deploy/.ssh/authorized_keys && chmod 600 /home/deploy/.ssh/authorized_keys"
```

Verify deploy user can access Docker without sudo:

```bash
ssh -i ~/.ssh/id_ed25519_swarm_deploy deploy@<server-ip> "docker info --format '{{.Swarm.LocalNodeState}}'"
```

After saving the private key in GitHub Actions secrets, you may remove your local private key if you do not need it for manual SSH.

### 4) Prepare GitHub Actions secrets

Required secrets:

- `SWARM_STACK_NAME` (example: `laravel-nuxtui`)
- `SSH_DEPLOY_PRIVATE_KEY` (contents of `~/.ssh/id_ed25519_swarm_deploy`)
- `SSH_DEPLOY_USER` (example: `deploy`)
- `SSH_REMOTE_HOSTNAME` (domain or IP)
- `SSH_REMOTE_KNOWN_HOSTS`
- `PRODUCTION_ENV_FILE_BASE64`

Generate `SSH_REMOTE_KNOWN_HOSTS` from local machine:

```bash
ssh-keyscan -p 22 -H <server-hostname-or-ip> 2>/dev/null | sort -u
```

`SSH_REMOTE_KNOWN_HOSTS` lets CI verify it is connecting to your real server (host key pinning), not an impostor.

### 5) Build production env file locally

Create `.env.production` on your local machine (never commit it).

It should include your production app/runtime settings such as:

- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_DOMAIN=example.com`
- `APP_URL=https://example.com`
- `APP_KEY=...`
- `DB_*`, mail, queue/cache/session settings, etc.

Base64 encode it for GitHub secret `PRODUCTION_ENV_FILE_BASE64`:

```bash
# macOS
base64 < .env.production | pbcopy

# Linux (single-line output)
base64 -w 0 .env.production
```

Your original command also works on macOS:

```bash
cat .env.production | base64 | pbcopy
```

### 6) What files must exist on the server?

For automated CI/CD in this setup: none from this app repository.

You only need:

- Docker + Swarm initialized
- Traefik stack running
- `traefik_proxy` overlay network
- SSH access for deploy user

The compose file and post-deploy commands run from the GitHub runner, not from the server.

### 7) Automated deployment flow

On push to `main`, the workflow:

1. Runs tests, lint, and type checks
2. Builds and pushes image to GHCR (`:sha` and `:main`)
3. Uses `serversideup/github-action-docker-swarm-deploy` for remote `docker stack deploy`
4. Decodes `PRODUCTION_ENV_FILE_BASE64` during deploy
5. Runs Laravel post-deploy commands over SSH (`appleboy/ssh-action`)

Post-deploy commands:

- post-deploy commands in app container:
  - `php artisan migrate --force`
  - `php artisan optimize:clear`
  - `php artisan optimize`
  - `php artisan inertia:check-ssr`

### 8) SSR process notes

- SSR is run in the app container using s6 overlay (`php artisan inertia:start-ssr`)
- This is a good fit for the `serversideup/php:*-fpm-nginx` base image
- Container health checks now validate both app readiness (`/up`) and SSR readiness

### 9) Rollback expectations

- Swarm can roll back failed rollouts when tasks fail health checks during update
- Swarm rollback does not revert destructive database migrations
- Keep migrations forward-safe and deploy them with care

### 10) Common operations

```bash
docker service ls
docker service ps <stack>_laravel --no-trunc
docker service logs -f <stack>_laravel
docker stack services <stack>
```

Notes:

- This stack uses rolling updates (`start-first`) and rollback on failure
- Do not use `php artisan down` in this deployment model
- `traefik_proxy` must exist and be shared by Traefik + app stack

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
