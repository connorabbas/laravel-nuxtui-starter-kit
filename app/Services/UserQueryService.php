<?php

namespace App\Services;

use App\Data\FilterOptionData;
use App\Data\PaginatedData;
use App\Data\UserData;
use App\Data\Users\UserIndexQueryData;
use App\Enums\UserSort;
use App\Models\User;
use App\Services\Concerns\NormalizesQueryValues;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

final class UserQueryService
{
    use NormalizesQueryValues;

    /**
     * @return LengthAwarePaginator<int, UserData>
     */
    public function paginate(UserIndexQueryData $query): LengthAwarePaginator
    {
        $search = $this->trimmedStringOrNull($query->search);
        $userIds = $query->userIds === [] ? null : $query->userIds;
        $createdFrom = $this->trimmedStringOrNull($query->createdFrom);
        $createdUntil = $this->trimmedStringOrNull($query->createdUntil);

        [$sortColumn, $sortDirection] = match ($query->sort) {
            UserSort::Newest => ['users.created_at', 'desc'],
            UserSort::Oldest => ['users.created_at', 'asc'],
            UserSort::NameAsc => ['users.name', 'asc'],
            UserSort::NameDesc => ['users.name', 'desc'],
            UserSort::EmailAsc => ['users.email', 'asc'],
            UserSort::EmailDesc => ['users.email', 'desc'],
        };

        return User::query()
            ->when($search, function (Builder $builder, string $search): void {
                $builder->where(function (Builder $builder) use ($search): void {
                    $builder
                        ->where('users.name', 'like', "%{$search}%")
                        ->orWhere('users.email', 'like', "%{$search}%");
                });
            })
            ->when($userIds !== null, function (Builder $builder) use ($userIds): void {
                $builder->whereIn('users.id', $userIds);
            })
            ->when($query->verified !== null, function (Builder $builder) use ($query): void {
                $query->verified
                    ? $builder->whereNotNull('users.email_verified_at')
                    : $builder->whereNull('users.email_verified_at');
            })
            ->when($createdFrom, function (Builder $builder, string $createdFrom): void {
                $builder->where('users.created_at', '>=', CarbonImmutable::parse($createdFrom)->startOfDay());
            })
            ->when($createdUntil, function (Builder $builder, string $createdUntil): void {
                $builder->where('users.created_at', '<=', CarbonImmutable::parse($createdUntil)->endOfDay());
            })
            ->orderBy($sortColumn, $sortDirection)
            ->orderBy('users.id')
            ->paginate(
                perPage: $query->perPage,
                page: $query->page,
            )
            ->through(fn (User $user) => UserData::fromModel($user))
            ->withQueryString();
    }

    /**
     * @return LengthAwarePaginator<int, UserData>
     */
    public function paginateUnfiltered(PaginatedData $query): LengthAwarePaginator
    {
        return User::query()
            ->latest('users.created_at')
            ->orderBy('users.id')
            ->paginate(
                perPage: $query->perPage,
                page: $query->page,
            )
            ->through(fn (User $user) => UserData::fromModel($user))
            ->withQueryString();
    }

    /**
     * @return array<int, FilterOptionData>
     */
    public function filterOptions(): array
    {
        return User::query()
            ->select(['id', 'name', 'email'])
            ->orderBy('name')
            ->orderBy('id')
            ->get()
            ->map(fn (User $user) => new FilterOptionData(
                value: $user->id,
                label: "{$user->name} ({$user->email})",
            ))
            ->all();
    }
}
