<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\PasswordUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class PasswordController extends Controller
{
    public function edit(): Response
    {
        return Inertia::render('settings/Password');
    }

    public function update(PasswordUpdateRequest $request): RedirectResponse
    {
        $request->authenticatedUser()->update([
            'password' => Hash::make($request->string('password')->toString()),
        ]);

        return to_route('settings.password.edit');
    }
}
