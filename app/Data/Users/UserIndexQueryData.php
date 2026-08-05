<?php

namespace App\Data\Users;

use App\Enums\UserSort;
use App\Models\User;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MergeValidationRules;
use Spatie\LaravelData\Attributes\Validation\AfterOrEqual;
use Spatie\LaravelData\Attributes\Validation\BeforeOrEqual;
use Spatie\LaravelData\Attributes\Validation\DateFormat;
use Spatie\LaravelData\Attributes\Validation\Enum;
use Spatie\LaravelData\Attributes\Validation\ListType;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
#[MergeValidationRules]
class UserIndexQueryData extends Data
{
    /**
     * @param  list<string>|null  $userIds
     */
    public function __construct(
        #[Min(1)]
        public int $page = 1,
        #[Min(1), Max(100)]
        public int $perPage = 10,
        #[Max(100)]
        public ?string $search = null,
        #[ListType, Max(20)]
        public ?array $userIds = null,
        public ?bool $verified = null,
        #[DateFormat('Y-m-d'), BeforeOrEqual('today')]
        public ?string $verifiedAt = null,
        #[DateFormat('Y-m-d'), BeforeOrEqual('today')]
        public ?string $createdFrom = null,
        #[DateFormat('Y-m-d'), AfterOrEqual('createdFrom'), BeforeOrEqual('today')]
        public ?string $createdUntil = null,
        #[Enum(UserSort::class)]
        public UserSort $sort = UserSort::Newest,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public static function rules($context = null): array
    {
        return [
            'userIds.*' => ['integer', 'distinct', Rule::exists(User::class, 'id')],
        ];
    }
}
