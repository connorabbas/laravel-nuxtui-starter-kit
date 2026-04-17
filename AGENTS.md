# AGENTS.md

Practical guide for coding agents operating in this repository.

## Project Snapshot

- Stack: Laravel 13 (PHP 8.3) + Inertia v2 + Vue 3 + TypeScript + Nuxt UI v4.
- Auth: Laravel Fortify.
- Bundler: Vite.
- Package manager: `npm` (not `yarn`/`pnpm`).
- Quality tools: ESLint 9, Laravel Pint, Pest 3 (PHPUnit 11 runtime).
- Test database defaults to in-memory SQLite in `phpunit.xml`.

## Setup Commands

- Install PHP dependencies: `composer install`
- Install frontend dependencies: `npm install`
- Create env file: `cp .env.example .env`
- Generate app key: `php artisan key:generate`
- Run migrations: `php artisan migrate`

## Build and Dev Commands

- Full local workflow (server, queue, logs, vite): `composer run dev`
- Full local workflow with SSR server: `composer run dev:ssr`
- Frontend dev server only: `npm run dev`
- Production assets build: `npm run build`
- SSR assets build: `npm run build:ssr`

## Lint, Format, and Typecheck Commands

- Frontend lint + auto-fix: `npm run lint`
- Frontend type-check: `npm run typecheck`
- Format PHP (changed files): `vendor/bin/pint --dirty`
- Format PHP (all files): `vendor/bin/pint`

## Test Commands (Pest)

- Full test suite: `php artisan test`
- Full suite via Composer script: `composer test`
- Compact output: `php artisan test --compact`
- Single test file (important):
  `php artisan test --compact tests/Feature/Auth/AuthenticationTest.php`
- Single test by name/filter (important):
  `php artisan test --compact --filter="users can authenticate using the login screen"`
- Single Pest case scoped to one file:
  `php artisan test --compact tests/Feature/Settings/ProfileUpdateTest.php --filter="profile information can be updated"`

## Recommended Verification Order

1. Run the most relevant single test(s) first.
2. Run `vendor/bin/pint --dirty` for touched PHP files.
3. Run `npm run lint` for touched frontend files.
4. Run `npm run typecheck` if TypeScript/Vue files changed.
5. Expand to broader test/build commands only as needed.

## Architecture and File Layout

- Follow existing Laravel 13 structure (`bootstrap/app.php` middleware config).
- Keep Inertia pages in `resources/js/pages`.
- Keep layouts in `resources/js/layouts`.
- Keep shared TS types in `resources/js/types`.
- Reuse existing components/actions before introducing new abstractions.
- Avoid creating new top-level directories without strong justification.

## PHP and Laravel Style Rules

- Use explicit parameter and return types on methods/functions.
- Always use braces for control structures.
- Prefer Form Request classes for non-trivial validation.
- Prefer Eloquent relationships/query builder over raw `DB::` usage.
- Keep mass assignment explicit on models (`$fillable`).
- Follow existing model cast pattern (`casts(): array` where used).
- Use constructor property promotion when constructors are needed.
- Do not add empty constructors.
- For user-facing messages/errors, use translation helpers like `__()`.
- Prefer named routes and `route()` URL generation.

## Data Contracts

- For structured data contracts in application code, use DTO/value object classes (prefer `spatie/laravel-data` `Data` objects).
- Do not use associative arrays for internal structured payloads.
- Flat/list arrays are allowed when type-hinted with generics (for example `array<int, FooData>`).
- Associative arrays are allowed only at framework boundaries where required (for example `Inertia::render(...)` props, Form Request `rules()`, config files, validation message maps).

## PHP Imports and Organization

- Keep `use` imports clean and consistent.
- Remove unused imports (Pint enforces this).
- Prefer importing symbols over repeated fully-qualified class names.
- Keep classes focused; avoid very large, mixed-responsibility methods.

## Testing Conventions (Pest)

- Write tests with Pest (`test(...)`/`it(...)`).
- Feature/integration flows belong in `tests/Feature`.
- Isolated logic belongs in `tests/Unit`.
- Use factories for model creation.
- Use `RefreshDatabase` when persistence isolation is required.
- Prefer expressive assertions (`assertOk`, `assertRedirect`, etc.).
- Cover happy paths and relevant failure paths for behavior changes.

## Vue, Inertia, and TypeScript Rules

- Use `<script setup lang="ts">` in Vue SFCs.
- Keep a single root element in templates.
- Use `@/` alias for app-local imports.
- Use Inertia primitives (`Link`, `useForm`, router events/visits) for SPA navigation/forms.
- Keep Ziggy usage compatible with app bootstrap in `resources/js/app.ts` and `resources/js/ssr.ts`.
- Prefer explicit interfaces/types for prop and payload shapes.
- Avoid introducing new `any` unless unavoidable.
- Keep reactive state clear and intent-revealing (`ref`, `reactive`, `computed`).

## Frontend Imports and Formatting

- ESLint is the source of truth for frontend style.
- Current enforced style includes:
  - 4-space indentation
  - no semicolons
  - Unix line endings
- Use `import type` for type-only imports.
- Let linting manage stylistic import cleanup.

## Global Formatting Rules

- `.editorconfig` defaults:
  - UTF-8
  - LF line endings
  - 4 spaces for most files
  - 2 spaces for `*.yml`/`*.yaml`
  - final newline required
- Pint rules include:
  - no unused imports
  - one import per statement
  - no trailing comma in multiline arrays/calls

## Naming Conventions

- PHP classes: `PascalCase`
- PHP methods/properties/variables: `camelCase`
- TS interfaces/types/components: `PascalCase`
- Vue page/layout component filenames: `PascalCase`.
- Route and test names should be descriptive and behavior-oriented.

## Error Handling Expectations

- Validate early; use Laravel-native validation/auth flows.
- Do not swallow unexpected exceptions silently.
- Keep fallback behavior explicit when catching exceptions.
- Return/display user-safe error messages.
- Do not expose stack traces or internal details to end users.
- In frontend forms, model submission/loading/error states clearly.

## Agent Behavior Guidelines

- Make minimal, targeted changes that match nearby conventions.
- Prefer updating existing files/components over broad refactors.
- Do not change dependencies or major architecture without explicit request.
- Run the smallest relevant verification commands before finishing.
- If frontend changes are not visible, run `npm run build` or ask to run `npm run dev` / `composer run dev`.

## Cursor / Copilot Rules

- `.cursorrules`: not found.
- `.cursor/rules/`: not found.
- `.github/copilot-instructions.md`: not found.
- If any are added later, merge their instructions into this file and treat them as high priority.

## Recommended Verification Order

1. Run the most relevant single test(s) first.
2. Run `vendor/bin/pint --dirty` for touched PHP files.
3. Run `npm run lint` and `npm run typecheck` for frontend changes.
4. Broaden to larger test/build checks only as needed.

===

<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to ensure the best experience when building Laravel applications.

## Foundational Context

This application is a Laravel application and its main Laravel ecosystems package & versions are below. You are an expert with them all. Ensure you abide by these specific packages & versions.

- php - 8.4
- inertiajs/inertia-laravel (INERTIA_LARAVEL) - v2
- laravel/fortify (FORTIFY) - v1
- laravel/framework (LARAVEL) - v13
- laravel/prompts (PROMPTS) - v0
- tightenco/ziggy (ZIGGY) - v2
- larastan/larastan (LARASTAN) - v3
- laravel/boost (BOOST) - v2
- laravel/mcp (MCP) - v0
- laravel/pail (PAIL) - v1
- laravel/pint (PINT) - v1
- laravel/sail (SAIL) - v1
- pestphp/pest (PEST) - v4
- phpunit/phpunit (PHPUNIT) - v12
- @inertiajs/vue3 (INERTIA_VUE) - v2
- vue (VUE) - v3
- eslint (ESLINT) - v9

## Skills Activation

This project has domain-specific skills available. You MUST activate the relevant skill whenever you work in that domain—don't wait until you're stuck.

- `laravel-best-practices` — Apply this skill whenever writing, reviewing, or refactoring Laravel PHP code. This includes creating or modifying controllers, models, migrations, form requests, policies, jobs, scheduled commands, service classes, and Eloquent queries. Triggers for N+1 and query performance issues, caching strategies, authorization and security patterns, validation, error handling, queue and job configuration, route definitions, and architectural decisions. Also use for Laravel code reviews and refactoring existing Laravel code to follow best practices. Covers any task involving Laravel backend PHP code patterns.
- `pest-testing` — Use this skill for Pest PHP testing in Laravel projects only. Trigger whenever any test is being written, edited, fixed, or refactored — including fixing tests that broke after a code change, adding assertions, converting PHPUnit to Pest, adding datasets, and TDD workflows. Always activate when the user asks how to write something in Pest, mentions test files or directories (tests/Feature, tests/Unit, tests/Browser), or needs browser testing, smoke testing multiple pages for JS errors, or architecture tests. Covers: it()/expect() syntax, datasets, mocking, browser testing (visit/click/fill), smoke testing, arch(), Livewire component tests, RefreshDatabase, and all Pest 4 features. Do not use for factories, seeders, migrations, controllers, models, or non-test PHP code.
- `inertia-vue-development` — Develops Inertia.js v2 Vue client-side applications. Activates when creating Vue pages, forms, or navigation; using <Link>, <Form>, useForm, or router; working with deferred props, prefetching, or polling; or when user mentions Vue with Inertia, Vue pages, Vue forms, or Vue navigation.
- `fortify-development` — Laravel Fortify headless authentication backend development. Activate when implementing authentication features including login, registration, password reset, email verification, two-factor authentication (2FA/TOTP), profile updates, headless auth, authentication scaffolding, or auth guards in Laravel applications.

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `npm run build`, `npm run dev`, or `composer run dev`. Ask them.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

## Replies

- Be concise in your explanations - focus on what's important rather than explaining obvious details.

=== boost rules ===

# Laravel Boost

## Tools

- Laravel Boost is an MCP server with tools designed specifically for this application. Prefer Boost tools over manual alternatives like shell commands or file reads.
- Use `database-query` to run read-only queries against the database instead of writing raw SQL in tinker.
- Use `database-schema` to inspect table structure before writing migrations or models.
- Use `get-absolute-url` to resolve the correct scheme, domain, and port for project URLs. Always use this before sharing a URL with the user.
- Use `browser-logs` to read browser logs, errors, and exceptions. Only recent logs are useful, ignore old entries.

## Searching Documentation (IMPORTANT)

- Always use `search-docs` before making code changes. Do not skip this step. It returns version-specific docs based on installed packages automatically.
- Pass a `packages` array to scope results when you know which packages are relevant.
- Use multiple broad, topic-based queries: `['rate limiting', 'routing rate limiting', 'routing']`. Expect the most relevant results first.
- Do not add package names to queries because package info is already shared. Use `test resource table`, not `filament 4 test resource table`.

### Search Syntax

1. Use words for auto-stemmed AND logic: `rate limit` matches both "rate" AND "limit".
2. Use `"quoted phrases"` for exact position matching: `"infinite scroll"` requires adjacent words in order.
3. Combine words and phrases for mixed queries: `middleware "rate limit"`.
4. Use multiple queries for OR logic: `queries=["authentication", "middleware"]`.

## Artisan

- Run Artisan commands directly via the command line (e.g., `php artisan route:list`). Use `php artisan list` to discover available commands and `php artisan [command] --help` to check parameters.
- Inspect routes with `php artisan route:list`. Filter with: `--method=GET`, `--name=users`, `--path=api`, `--except-vendor`, `--only-vendor`.
- Read configuration values using dot notation: `php artisan config:show app.name`, `php artisan config:show database.default`. Or read config files directly from the `config/` directory.
- To check environment variables, read the `.env` file directly.

## Tinker

- Execute PHP in app context for debugging and testing code. Do not create models without user approval, prefer tests with factories instead. Prefer existing Artisan commands over custom tinker code.
- Always use single quotes to prevent shell expansion: `php artisan tinker --execute 'Your::code();'`
  - Double quotes for PHP strings inside: `php artisan tinker --execute 'User::where("active", true)->count();'`

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.
- Use PHP 8 constructor property promotion: `public function __construct(public GitHub $github) { }`. Do not leave empty zero-parameter `__construct()` methods unless the constructor is private.
- Use explicit return type declarations and type hints for all method parameters: `function isAccessible(User $user, ?string $path = null): bool`
- Use TitleCase for Enum keys: `FavoritePerson`, `BestLake`, `Monthly`.
- Prefer PHPDoc blocks over inline comments. Only add inline comments for exceptionally complex logic.
- Use array shape type definitions in PHPDoc blocks.

=== tests rules ===

# Test Enforcement

- Every change must be programmatically tested. Write a new test or update an existing test, then run the affected tests to make sure they pass.
- Run the minimum number of tests needed to ensure code quality and speed. Use `php artisan test --compact` with a specific filename or filter.

=== inertia-laravel/core rules ===

# Inertia

- Inertia creates fully client-side rendered SPAs without modern SPA complexity, leveraging existing server-side patterns.
- Components live in `resources/js/pages` (unless specified in `vite.config.js`). Use `Inertia::render()` for server-side routing instead of Blade views.
- ALWAYS use `search-docs` tool for version-specific Inertia documentation and updated code examples.
- IMPORTANT: Activate `inertia-vue-development` when working with Inertia Vue client-side patterns.

# Inertia v2

- Use all Inertia features from v1 and v2. Check the documentation before making changes to ensure the correct approach.
- New features: deferred props, infinite scroll, merging props, polling, prefetching, once props, flash data.
- When using deferred props, add an empty state with a pulsing or animated skeleton.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using `php artisan list` and check their parameters with `php artisan [command] --help`.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `php artisan make:model --help` to check the available options.

## APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

## Vite Error

- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test --format agent`, simply run `vendor/bin/pint --format agent` to fix any formatting issues.

=== pest/core rules ===

## Pest

- This project uses Pest for testing. Create tests: `php artisan make:test --pest {name}`.
- Run tests: `php artisan test --compact` or filter: `php artisan test --compact --filter=testName`.
- Do NOT delete tests without approval.

=== inertia-vue/core rules ===

# Inertia + Vue

Vue components must have a single root element.
- IMPORTANT: Activate `inertia-vue-development` when working with Inertia Vue client-side patterns.

=== laravel/fortify rules ===

# Laravel Fortify

- Fortify is a headless authentication backend that provides authentication routes and controllers for Laravel applications.
- IMPORTANT: Always use the `search-docs` tool for detailed Laravel Fortify patterns and documentation.
- IMPORTANT: Activate `developing-with-fortify` skill when working with Fortify authentication features.

</laravel-boost-guidelines>
