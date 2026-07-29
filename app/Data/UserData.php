<?php

namespace App\Data;

use Carbon\CarbonInterface;
use App\Models\User;
use Carbon\CarbonImmutable;
use LogicException;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class UserData extends Data
{
    public function __construct(
        public int $id,
        public string $name,
        public string $email,
        public CarbonImmutable|string|null $emailVerifiedAt,
        public CarbonImmutable|string $createdAt,
        public CarbonImmutable|string $updatedAt,
    ) {
    }

    public static function fromModel(User $user): self
    {
        $createdAt = $user->created_at;
        $updatedAt = $user->updated_at;

        if ($createdAt === null || $updatedAt === null) {
            throw new LogicException('User timestamps must be present before transforming to UserData.');
        }

        return new self(
            id: $user->id,
            name: $user->name,
            email: $user->email,
            emailVerifiedAt: self::formatDate($user->email_verified_at),
            createdAt: self::formatRequiredDate($createdAt),
            updatedAt: self::formatRequiredDate($updatedAt),
        );
    }

    private static function formatDate(CarbonInterface|string|null $date): ?string
    {
        if ($date === null || is_string($date)) {
            return $date;
        }

        return $date->toJSON() ?? $date->format(DATE_ATOM);
    }

    private static function formatRequiredDate(CarbonInterface|string $date): string
    {
        if (is_string($date)) {
            return $date;
        }

        return $date->toJSON() ?? $date->format(DATE_ATOM);
    }
}
