<?php

namespace App\Http\Controllers\Examples;

use App\Http\Controllers\Controller;
use App\Services\Examples\UserDirectoryQueryService;
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
        return Inertia::render('examples/paginator/users', [
            'users' => $this->queryService->paginate($request),
            'filterDefinitions' => $this->queryService->frontendFilterDefinitions(),
            'accountStatusOptions' => $this->queryService->accountStatusOptionsFrontend(),
            'accountProviderOptions' => $this->queryService->accountProviderOptionsFrontend(),
        ]);
    }

    public function table(Request $request): Response
    {
        return Inertia::render('examples/table/users', [
            'users' => $this->queryService->paginate($request),
            'filterDefinitions' => $this->queryService->frontendFilterDefinitions(),
            'accountStatusOptions' => $this->queryService->accountStatusOptionsFrontend(),
            'accountProviderOptions' => $this->queryService->accountProviderOptionsFrontend(),
        ]);
    }
}
