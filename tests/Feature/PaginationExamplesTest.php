<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);

test('guests are redirected from pagination examples', function () {
    $this->get(route('pagination.table', absolute: false))->assertRedirect(route('login', absolute: false));
    $this->get(route('pagination.cards', absolute: false))->assertRedirect(route('login', absolute: false));
});

test('authenticated users can view the pagination table example', function () {
    $user = User::factory()->create();
    User::factory()->count(14)->create();

    $this->actingAs($user)
        ->get(route('pagination.table', absolute: false))
        ->assertOk()
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('pagination/Table', false)
                ->has('users.data', 10)
                ->where('users.current_page', 1)
                ->where('users.per_page', 10)
                ->where('users.total', 15)
                ->where('query.page', 1)
                ->where('query.perPage', 10)
                ->where('query.search', null)
                ->where('query.userIds', null)
                ->where('query.verified', null)
                ->where('query.createdFrom', null)
                ->where('query.createdUntil', null)
                ->where('query.sort', 'newest')
                ->has('userFilterOptions', 15)
        );
});

test('users can be filtered and sorted through typed query params', function () {
    $user = User::factory()->create(['name' => 'Current User', 'email' => 'current@example.test']);

    User::factory()->create([
        'name' => 'Alice Example',
        'email' => 'alice@example.test',
    ]);

    User::factory()->create([
        'name' => 'Bob Example',
        'email' => 'bob@example.test',
    ]);

    $this->actingAs($user)
        ->get(route('pagination.table', [
            'search' => 'example',
            'sort' => 'email_asc',
            'perPage' => 25,
        ], false))
        ->assertOk()
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('pagination/Table', false)
                ->has('users.data', 3)
                ->where('users.data.0.email', 'alice@example.test')
                ->where('query.search', 'example')
                ->where('query.sort', 'email_asc')
                ->where('query.perPage', 25)
                ->where('query.page', 1)
        );
});

test('users can be filtered by verification state and created date range', function () {
    $user = User::factory()->create([
        'created_at' => '2026-06-01 12:00:00',
    ]);

    User::factory()->create([
        'name' => 'Verified Recent',
        'email_verified_at' => now(),
        'created_at' => '2026-07-10 12:00:00',
    ]);

    User::factory()->unverified()->create([
        'name' => 'Unverified Recent',
        'created_at' => '2026-07-11 12:00:00',
    ]);

    User::factory()->create([
        'name' => 'Verified Old',
        'email_verified_at' => now(),
        'created_at' => '2026-06-01 12:00:00',
    ]);

    $this->actingAs($user)
        ->get(route('pagination.table', [
            'verified' => true,
            'createdFrom' => '2026-07-01',
            'createdUntil' => '2026-07-31',
        ], false))
        ->assertOk()
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('pagination/Table', false)
                ->has('users.data', 1)
                ->where('users.data.0.name', 'Verified Recent')
                ->where('query.verified', true)
                ->where('query.createdFrom', '2026-07-01')
                ->where('query.createdUntil', '2026-07-31')
        );
});

test('users can be filtered by multiple selected users', function () {
    $user = User::factory()->create(['name' => 'Current User']);

    $alpha = User::factory()->create(['name' => 'Alpha Selected']);
    User::factory()->create(['name' => 'Beta Excluded']);
    $charlie = User::factory()->create(['name' => 'Charlie Selected']);

    $this->actingAs($user)
        ->get(route('pagination.table', [
            'userIds' => [$charlie->id, $alpha->id],
            'sort' => 'name_asc',
            'perPage' => 25,
        ], false))
        ->assertOk()
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('pagination/Table', false)
                ->has('users.data', 2)
                ->where('users.data.0.name', 'Alpha Selected')
                ->where('users.data.1.name', 'Charlie Selected')
                ->where('query.userIds', [$charlie->id, $alpha->id])
                ->where('query.sort', 'name_asc')
        );
});

test('empty selected users are normalized back to null', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('pagination.table', absolute: false).'?userIds[]=')
        ->assertOk()
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('pagination/Table', false)
                ->where('query.userIds', null)
        );
});

test('authenticated users can view the pagination cards example', function () {
    $user = User::factory()->create();
    User::factory()->count(14)->create();

    $this->actingAs($user)
        ->get(route('pagination.cards', absolute: false))
        ->assertOk()
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('pagination/Cards', false)
                ->has('users.data', 10)
                ->has('userFilterOptions', 15)
                ->where('query.userIds', null)
                ->where('query.sort', 'newest')
        );
});

test('invalid users query params are rejected', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->from(route('pagination.table', absolute: false))
        ->get(route('pagination.table', [
            'page' => 0,
            'perPage' => 500,
            'sort' => 'password',
            'userIds' => ['invalid-user'],
            'verified' => 'maybe',
            'createdFrom' => 'not-a-date',
            'createdUntil' => '2026-01-01',
        ], false))
        ->assertRedirect(route('pagination.table', absolute: false))
        ->assertSessionHasErrors(['page', 'perPage', 'sort', 'userIds.0', 'verified', 'createdFrom']);
});

test('created until must be after or equal to created from', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->from(route('pagination.table', absolute: false))
        ->get(route('pagination.table', [
            'createdFrom' => '2026-07-31',
            'createdUntil' => '2026-07-01',
        ], false))
        ->assertRedirect(route('pagination.table', absolute: false))
        ->assertSessionHasErrors(['createdUntil']);
});

test('pagination table supports partial reloads for paginated props', function () {
    $user = User::factory()->create();
    User::factory()->count(14)->create();

    $this->actingAs($user)
        ->get(route('pagination.table', absolute: false))
        ->assertOk()
        ->assertInertia(
            fn (Assert $page) => $page
                ->has('users')
                ->has('query')
                ->reloadOnly(
                    ['users', 'query'],
                    fn (Assert $reload) => $reload
                        ->has('users')
                        ->has('query')
                        ->missing('auth')
                )
        );
});
