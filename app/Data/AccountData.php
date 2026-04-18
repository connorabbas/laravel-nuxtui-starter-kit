<?php

namespace App\Data;

use App\Models\Account;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class AccountData extends Data
{
    public function __construct(
        public int $id,
        public string $name,
        public ?string $provider,
        public string $status,
        public string $balance,
        public Carbon|CarbonImmutable|string|null $openedAt,
    ) {
    }

    public static function fromModel(Account $account): self
    {
        $openedAt = $account->opened_at;

        if ($openedAt instanceof CarbonInterface) {
            $openedAt = $openedAt->toDateString();
        }

        return new self(
            id: $account->id,
            name: $account->name,
            provider: $account->provider,
            status: $account->status,
            balance: (string) $account->balance,
            openedAt: $openedAt,
        );
    }
}
