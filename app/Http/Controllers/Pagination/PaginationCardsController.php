<?php

namespace App\Http\Controllers\Pagination;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\UserIndexRequest;
use App\Queries\Users\UserIndexQuery;
use Inertia\Inertia;
use Inertia\Response;

class PaginationCardsController extends Controller
{
    public function __invoke(UserIndexRequest $request, UserIndexQuery $users): Response
    {
        $query = $request->queryData();

        return Inertia::render('pagination/Cards', [
            'users' => fn () => $users->paginate($query),
            'query' => $query,
        ]);
    }
}
