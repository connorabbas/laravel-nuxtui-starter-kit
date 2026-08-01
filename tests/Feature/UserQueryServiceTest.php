<?php

use App\Data\PaginatedData;
use App\Data\UserData;
use App\Data\Users\UserIndexQueryData;
use App\Enums\UserSort;
use App\Models\User;
use App\Services\UserQueryService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('it trims search terms when paginating users directly', function () {
    User::factory()->create([
        'name' => 'Alice Example',
        'email' => 'alice@example.test',
    ]);

    User::factory()->create([
        'name' => 'Bob Example',
        'email' => 'bob@example.test',
    ]);

    $users = app(UserQueryService::class)->paginate(new UserIndexQueryData(
        perPage: 25,
        search: '  Alice  ',
    ));

    expect($users->total())->toBe(1)
        ->and($users->items()[0])->toBeInstanceOf(UserData::class)
        ->and($users->items()[0]->email)->toBe('alice@example.test');
});

test('it ignores whitespace only search terms', function () {
    User::factory()->count(2)->create();

    $users = app(UserQueryService::class)->paginate(new UserIndexQueryData(
        perPage: 25,
        search: '   ',
    ));

    expect($users->total())->toBe(2);
});

test('it applies zero search terms', function () {
    User::factory()->create([
        'name' => '0 User',
        'email' => '0@example.test',
    ]);

    User::factory()->create([
        'name' => 'Other User',
        'email' => 'other@example.test',
    ]);

    $users = app(UserQueryService::class)->paginate(new UserIndexQueryData(
        perPage: 25,
        search: '0',
    ));

    expect($users->total())->toBe(1)
        ->and($users->items()[0]->email)->toBe('0@example.test');
});

test('it converts selected user ids to integers before filtering', function () {
    $alpha = User::factory()->create(['name' => 'Alpha Selected']);
    User::factory()->create(['name' => 'Beta Excluded']);
    $charlie = User::factory()->create(['name' => 'Charlie Selected']);

    $users = app(UserQueryService::class)->paginate(new UserIndexQueryData(
        perPage: 25,
        userIds: [(string) $charlie->id, (string) $alpha->id],
        sort: UserSort::NameAsc,
    ));

    expect($users->total())->toBe(2)
        ->and($users->items()[0]->name)->toBe('Alpha Selected')
        ->and($users->items()[1]->name)->toBe('Charlie Selected');
});

test('it rejects invalid selected user ids when called without validation', function () {
    app(UserQueryService::class)->paginate(new UserIndexQueryData(
        userIds: ['invalid-user'],
    ));
})->throws(InvalidArgumentException::class);

test('it paginates unfiltered users newest first', function () {
    User::factory()->create([
        'email' => 'oldest@example.test',
        'created_at' => '2026-07-01 12:00:00',
    ]);

    User::factory()->create([
        'email' => 'newest@example.test',
        'created_at' => '2026-07-31 12:00:00',
    ]);

    $users = app(UserQueryService::class)->paginateUnfiltered(new PaginatedData(perPage: 1));

    expect($users->total())->toBe(2)
        ->and($users->items()[0])->toBeInstanceOf(UserData::class)
        ->and($users->items()[0]->email)->toBe('newest@example.test');
});

test('it returns sorted user filter options', function () {
    User::factory()->create([
        'name' => 'Beta User',
        'email' => 'beta@example.test',
    ]);

    User::factory()->create([
        'name' => 'Alpha User',
        'email' => 'alpha@example.test',
    ]);

    $options = app(UserQueryService::class)->filterOptions();

    expect($options)->toHaveCount(2)
        ->and($options[0]->value)->toBeString()
        ->and($options[0]->label)->toBe('Alpha User (alpha@example.test)')
        ->and($options[1]->label)->toBe('Beta User (beta@example.test)');
});
