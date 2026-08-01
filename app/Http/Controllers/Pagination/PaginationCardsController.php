<?php

namespace App\Http\Controllers\Pagination;

use App\Data\Users\UserIndexQueryData;
use App\Http\Controllers\Controller;
use App\Queries\Users\UserIndexQuery;
use Inertia\Inertia;
use Inertia\Response;

class PaginationCardsController extends Controller
{
    public function __invoke(UserIndexQueryData $query, UserIndexQuery $users): Response
    {
        return Inertia::render('pagination/Cards', [
            'users' => fn () => $users->paginate($query),
            'userFilterOptions' => Inertia::defer(fn () => $users->filterOptions()),
            'query' => $query,
        ]);
    }
}
