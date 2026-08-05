<?php

namespace App\Http\Controllers\Pagination;

use App\Data\Users\UserIndexQueryData;
use App\Http\Controllers\Controller;
use App\Services\UserQueryService;
use Inertia\Inertia;
use Inertia\Response;

class PaginationCardsController extends Controller
{
    public function __invoke(UserIndexQueryData $query, UserQueryService $userQueryService): Response
    {
        return Inertia::render('pagination/Cards', [
            'users' => fn () => $userQueryService->paginate($query),
            'userFilterOptions' => fn () => $userQueryService->filterOptions(),
            'query' => $query,
        ]);
    }
}
