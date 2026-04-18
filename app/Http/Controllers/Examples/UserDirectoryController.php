<?php

namespace App\Http\Controllers\Examples;

use App\Data\UserData;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Examples\UserDirectoryQueryService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UserDirectoryController extends Controller
{
    public function __construct(private readonly UserDirectoryQueryService $queryService)
    {
    }

    public function paginator(Request $request): Response
    {
        /** @var LengthAwarePaginator<int, User> $users */
        $users = $this->queryService->paginate($request);

        /** @var LengthAwarePaginator<int, UserData> $users */
        $users = $users->through(fn (User $user): UserData => UserData::fromModel($user));

        return Inertia::render('examples/paginator/Index', [
            'users' => $users,
            'filterDefinitions' => $this->queryService->frontendFilterDefinitions(),
            'accountStatusOptions' => $this->queryService->accountStatusOptionsFrontend(),
            'accountProviderOptions' => $this->queryService->accountProviderOptionsFrontend(),
        ]);
    }

    public function table(Request $request): Response
    {
        /** @var LengthAwarePaginator<int, User> $users */
        $users = $this->queryService->paginate($request);

        /** @var LengthAwarePaginator<int, UserData> $users */
        $users = $users->through(fn (User $user): UserData => UserData::fromModel($user));

        return Inertia::render('examples/table/Index', [
            'users' => $users,
            'filterDefinitions' => $this->queryService->frontendFilterDefinitions(),
            'accountStatusOptions' => $this->queryService->accountStatusOptionsFrontend(),
            'accountProviderOptions' => $this->queryService->accountProviderOptionsFrontend(),
        ]);
    }
}
