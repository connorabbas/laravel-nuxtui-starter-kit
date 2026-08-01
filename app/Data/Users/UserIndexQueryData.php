<?php

namespace App\Data\Users;

use App\Enums\UserSort;
use App\Models\User;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MergeValidationRules;
use Spatie\LaravelData\Attributes\Validation\AfterOrEqual;
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
     * @param  array<int, int>|null  $userIds
     */
    public function __construct(
        #[Min(1)]
        public int $page = 1,
        #[Min(1)]
        public int $perPage = 10,
        #[Max(100)]
        public ?string $search = null,
        #[ListType, Max(20)]
        public ?array $userIds = null,
        public ?bool $verified = null,
        #[DateFormat('Y-m-d')]
        public ?string $createdFrom = null,
        #[DateFormat('Y-m-d'), AfterOrEqual('createdFrom')]
        public ?string $createdUntil = null,
        #[Enum(UserSort::class)]
        public UserSort $sort = UserSort::Newest,
    ) {
    }

    /**
     * @param  array<string, mixed>  $properties
     * @return array<string, mixed>
     */
    public static function prepareForPipeline(array $properties): array
    {
        if (isset($properties['userIds']) && is_array($properties['userIds'])) {
            $userIds = array_values(array_filter(
                $properties['userIds'],
                static fn (mixed $userId): bool => $userId !== null && $userId !== '',
            ));

            $properties['userIds'] = $userIds === [] ? null : array_map(static function (mixed $userId): mixed {
                $integer = filter_var($userId, FILTER_VALIDATE_INT);

                return $integer === false ? $userId : $integer;
            }, $userIds);
        }

        return $properties;
    }

    /**
     * @return array<string, mixed>
     */
    public static function rules(): array
    {
        return [
            'userIds.*' => ['integer', 'distinct', Rule::exists(User::class, 'id')],
        ];
    }
}
