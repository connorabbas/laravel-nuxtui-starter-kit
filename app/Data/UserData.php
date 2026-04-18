<?php

namespace App\Data;

use App\Models\Account;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
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
        public Carbon|CarbonImmutable|string $createdAt,
        public Carbon|CarbonImmutable|string $updatedAt,
        /** @var array<int, AccountData>|null */
        public ?array $accounts = null,
        public ?int $accountsCount = null,
        public ?string $accountsSumBalance = null,
        public Carbon|CarbonImmutable|string|null $accountsMaxOpenedAt = null,
    ) {
    }

    public static function fromModel(User $user): self
    {
        $createdAt = $user->created_at;
        $updatedAt = $user->updated_at;
        $attributes = $user->getAttributes();

        $accounts = null;
        if ($user->relationLoaded('accounts')) {
            $accounts = $user->accounts
                ->map(fn (Account $account): AccountData => AccountData::fromModel($account))
                ->all();
        }

        $accountsCount = null;
        if (array_key_exists('accounts_count', $attributes) && $user->accounts_count !== null) {
            $accountsCount = (int) $user->accounts_count;
        }

        $accountsSumBalance = null;
        if (array_key_exists('accounts_sum_balance', $attributes) && $user->accounts_sum_balance !== null) {
            $accountsSumBalance = (string) $user->accounts_sum_balance;
        }

        $accountsMaxOpenedAt = null;
        if (array_key_exists('accounts_max_opened_at', $attributes) && $user->accounts_max_opened_at !== null) {
            $accountsMaxOpenedAt = $user->accounts_max_opened_at;

            if ($accountsMaxOpenedAt instanceof CarbonInterface) {
                $accountsMaxOpenedAt = $accountsMaxOpenedAt->toDateString();
            }
        }

        if ($createdAt === null || $updatedAt === null) {
            throw new LogicException('User timestamps must be present before transforming to UserData.');
        }

        return new self(
            id: $user->id,
            name: $user->name,
            email: $user->email,
            emailVerifiedAt: $user->email_verified_at,
            createdAt: $createdAt,
            updatedAt: $updatedAt,
            accounts: $accounts,
            accountsCount: $accountsCount,
            accountsSumBalance: $accountsSumBalance,
            accountsMaxOpenedAt: $accountsMaxOpenedAt,
        );
    }
}
