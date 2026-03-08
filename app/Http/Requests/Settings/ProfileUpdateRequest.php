<?php

namespace App\Http\Requests\Settings;

use App\Http\Requests\AuthenticatedFormRequest;
use App\Models\User;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends AuthenticatedFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->authenticatedUser()->id),
            ],
        ];
    }
}
