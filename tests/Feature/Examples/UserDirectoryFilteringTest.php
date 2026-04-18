<?php

use App\Models\User;
use App\Models\Account;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);

beforeEach(function () {
    $alpha = User::factory()->create([
        'name' => 'Alpha Adams',
        'email' => 'alpha@example.com',
        'created_at' => '2026-01-10 08:00:00',
    ]);

    $bravo = User::factory()->create([
        'name' => 'Bravo Brown',
        'email' => 'bravo@example.com',
        'created_at' => '2026-02-15 08:00:00',
    ]);

    $charlie = User::factory()->create([
        'name' => 'Charlie Clark',
        'email' => 'charlie@example.com',
        'created_at' => '2026-03-20 08:00:00',
    ]);

    Account::factory()->for($alpha)->create([
        'status' => 'active',
        'provider' => 'Stripe',
        'balance' => 200,
        'opened_at' => '2026-01-12',
    ]);

    Account::factory()->for($bravo)->count(2)->sequence([
        'status' => 'active',
        'provider' => 'PayPal',
        'balance' => 500,
        'opened_at' => '2026-02-18',
    ], [
        'status' => 'suspended',
        'provider' => 'Wise',
        'balance' => 1200,
        'opened_at' => '2026-02-28',
    ])->create();

    Account::factory()->for($charlie)->create([
        'status' => 'closed',
        'provider' => 'Square',
        'balance' => 50,
        'opened_at' => '2026-03-10',
    ]);
});

it('applies contains filtering for name', function () {
    $response = $this->actingAs(User::query()->firstOrFail())
        ->get(route('examples.paginator.users', [
            'filter' => [
                'name' => [
                    'op' => 'contains',
                    'value' => 'Bravo',
                ],
            ],
        ], false));

    $response->assertSuccessful();
    $response->assertInertia(
        fn (Assert $page) => $page
            ->component('examples/paginator/Index', false)
            ->where('users.total', 1)
            ->where('users.data.0.name', 'Bravo Brown')
    );
});

it('applies between filtering for created date', function () {
    $response = $this->actingAs(User::query()->firstOrFail())
        ->get(route('examples.paginator.users', [
            'filter' => [
                'created_at' => [
                    'op' => 'between',
                    'value' => ['2026-01-01', '2026-02-28'],
                ],
            ],
        ], false));

    $response->assertSuccessful();
    $response->assertInertia(
        fn (Assert $page) => $page
            ->component('examples/paginator/Index', false)
            ->where('users.total', 2)
    );
});

it('applies descending sort and pagination size', function () {
    $response = $this->actingAs(User::query()->firstOrFail())
        ->get(route('examples.table.users', [
            'sort' => '-name',
            'perPage' => 20,
        ], false));

    $response->assertSuccessful();
    $response->assertInertia(
        fn (Assert $page) => $page
            ->component('examples/table/Index', false)
            ->where('users.per_page', 20)
            ->where('users.data.0.name', 'Charlie Clark')
    );
});

it('ignores disallowed match modes', function () {
    $response = $this->actingAs(User::query()->firstOrFail())
        ->get(route('examples.paginator.users', [
            'filter' => [
                'name' => [
                    'op' => 'dateBefore',
                    'value' => '2026-01-01',
                ],
            ],
        ], false));

    $response->assertSuccessful();
    $response->assertInertia(
        fn (Assert $page) => $page
            ->component('examples/paginator/Index', false)
            ->where('users.total', 3)
    );
});

it('filters users by related account status', function () {
    $response = $this->actingAs(User::query()->firstOrFail())
        ->get(route('examples.table.users', [
            'filter' => [
                'accounts_status' => [
                    'op' => 'equals',
                    'value' => 'suspended',
                ],
            ],
        ], false));

    $response->assertSuccessful();
    $response->assertInertia(
        fn (Assert $page) => $page
            ->component('examples/table/Index', false)
            ->where('users.total', 1)
            ->where('users.data.0.name', 'Bravo Brown')
    );
});

it('sorts users by related account aggregate count', function () {
    $response = $this->actingAs(User::query()->firstOrFail())
        ->get(route('examples.table.users', [
            'sort' => '-accounts_count',
            'perPage' => 20,
        ], false));

    $response->assertSuccessful();
    $response->assertInertia(
        fn (Assert $page) => $page
            ->component('examples/table/Index', false)
            ->where('users.data.0.name', 'Bravo Brown')
            ->where('users.data.0.accountsCount', 2)
    );
});

it('filters users by related account providers using in mode', function () {
    $response = $this->actingAs(User::query()->firstOrFail())
        ->get(route('examples.table.users', [
            'filter' => [
                'accounts_provider' => [
                    'op' => 'in',
                    'value' => ['Wise', 'Square'],
                ],
            ],
        ], false));

    $response->assertSuccessful();
    $response->assertInertia(
        fn (Assert $page) => $page
            ->component('examples/table/Index', false)
            ->where('users.total', 2)
    );
});
