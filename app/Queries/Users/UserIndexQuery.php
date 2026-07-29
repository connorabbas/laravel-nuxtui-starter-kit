<?php

namespace App\Queries\Users;

use App\Data\UserData;
use App\Data\Users\UserIndexQueryData;
use App\Enums\UserSort;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

final class UserIndexQuery
{
    /**
     * @return LengthAwarePaginator<int, UserData>
     */
    public function paginate(UserIndexQueryData $query): LengthAwarePaginator
    {
        [$sortColumn, $sortDirection] = match ($query->sort) {
            UserSort::Newest => ['users.created_at', 'desc'],
            UserSort::Oldest => ['users.created_at', 'asc'],
            UserSort::NameAsc => ['users.name', 'asc'],
            UserSort::NameDesc => ['users.name', 'desc'],
            UserSort::EmailAsc => ['users.email', 'asc'],
            UserSort::EmailDesc => ['users.email', 'desc'],
        };

        return User::query()
            ->when($query->search, function (Builder $builder, string $search): void {
                $builder->where(function (Builder $builder) use ($search): void {
                    $builder
                        ->where('users.name', 'like', "%{$search}%")
                        ->orWhere('users.email', 'like', "%{$search}%");
                });
            })
            ->when($query->verified !== null, function (Builder $builder) use ($query): void {
                $query->verified
                    ? $builder->whereNotNull('users.email_verified_at')
                    : $builder->whereNull('users.email_verified_at');
            })
            ->when($query->createdFrom, function (Builder $builder, string $createdFrom): void {
                $builder->whereDate('users.created_at', '>=', $createdFrom);
            })
            ->when($query->createdUntil, function (Builder $builder, string $createdUntil): void {
                $builder->whereDate('users.created_at', '<=', $createdUntil);
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
}
