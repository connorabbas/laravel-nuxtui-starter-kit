<?php

namespace App\Http\Controllers\Pagination;

use App\Data\PaginatedData;
use App\Http\Controllers\Controller;
use App\Services\UserQueryService;
use Inertia\Inertia;
use Inertia\Response;

class BasicPaginationTableController extends Controller
{
    public function __invoke(PaginatedData $query, UserQueryService $userQueryService): Response
    {
        return Inertia::render('pagination/basic/Table', [
            'users' => fn () => $userQueryService->paginateUnfiltered($query),
            'query' => $query,
        ]);
    }
}
