<?php

use App\Data\UserData;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('user data transforms dates with the configured data transformer', function () {
    $createdAt = CarbonImmutable::parse('2026-07-31 12:34:56', 'UTC');
    $updatedAt = CarbonImmutable::parse('2026-07-31 13:34:56', 'UTC');

    $data = new UserData(
        id: 1,
        name: 'Taylor Otwell',
        email: 'taylor@example.test',
        emailVerifiedAt: null,
        createdAt: $createdAt,
        updatedAt: $updatedAt,
    );

    expect($data->toArray())
        ->emailVerifiedAt->toBeNull()
        ->createdAt->toBe($createdAt->format(DATE_ATOM))
        ->updatedAt->toBe($updatedAt->format(DATE_ATOM));
});

test('from model uses the same date transformation path as direct construction', function () {
    $createdAt = CarbonImmutable::parse('2026-07-31 12:34:56', 'UTC');
    $updatedAt = CarbonImmutable::parse('2026-07-31 13:34:56', 'UTC');
    $verifiedAt = CarbonImmutable::parse('2026-07-31 14:34:56', 'UTC');

    $user = User::factory()->create([
        'name' => 'Taylor Otwell',
        'email' => 'taylor@example.test',
        'email_verified_at' => $verifiedAt,
        'created_at' => $createdAt,
        'updated_at' => $updatedAt,
    ]);

    $fromModel = UserData::fromModel($user)->toArray();
    $direct = (new UserData(
        id: $user->id,
        name: $user->name,
        email: $user->email,
        emailVerifiedAt: $user->email_verified_at,
        createdAt: $user->created_at,
        updatedAt: $user->updated_at,
    ))->toArray();

    expect($fromModel)->toBe($direct);
});

test('from model requires timestamps', function () {
    $user = new User([
        'name' => 'Taylor Otwell',
        'email' => 'taylor@example.test',
    ]);

    UserData::fromModel($user);
})->throws(LogicException::class, 'User timestamps must be present before transforming to UserData.');
