<?php

namespace App\Http\Requests\Settings;

use App\Http\Requests\AuthenticatedFormRequest;

class ProfileDeleteRequest extends AuthenticatedFormRequest
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
            'password' => ['required', 'string', 'current_password'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'password.current_password' => __('The provided password does not match your current password.'),
        ];
    }
}
