<?php

namespace App\Http\Controllers\Pagination;

use App\Data\PaginatedData;
use App\Http\Controllers\Controller;
use App\Queries\Users\UserIndexQuery;
use Inertia\Inertia;
use Inertia\Response;

class BasicPaginationTableController extends Controller
{
    public function __invoke(PaginatedData $query, UserIndexQuery $users): Response
    {
        return Inertia::render('pagination/basic/Table', [
            'users' => fn () => $users->paginateAll($query),
            'query' => $query,
        ]);
    }
}
