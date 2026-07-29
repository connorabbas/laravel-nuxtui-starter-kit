<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\UserIndexRequest;
use App\Queries\Users\UserIndexQuery;
use Inertia\Inertia;
use Inertia\Response;

class UserDirectoryController extends Controller
{
    public function __invoke(UserIndexRequest $request, UserIndexQuery $users): Response
    {
        $query = $request->queryData();

        return Inertia::render('users/Directory', [
            'users' => fn () => $users->paginate($query),
            'query' => $query,
        ]);
    }
}
