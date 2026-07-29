<?php

namespace App\Data\Users;

use App\Enums\UserSort;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class UserIndexQueryData extends Data
{
    public function __construct(
        public int $page = 1,
        public int $perPage = 10,
        public ?string $search = null,
        public ?bool $verified = null,
        public ?string $createdFrom = null,
        public ?string $createdUntil = null,
        public UserSort $sort = UserSort::Newest,
    ) {
    }
}
