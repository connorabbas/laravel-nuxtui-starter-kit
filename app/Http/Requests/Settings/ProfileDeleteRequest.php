<?php

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;

class ProfileDeleteRequest extends FormRequest
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
            'password' => ['required', 'string'],
        ];
    }

    public function validatePassword(): void
    {
        if (! Hash::check($this->string('password')->toString(), $this->user()->password)) {
            throw ValidationException::withMessages([
                'password' => [__('The provided password does not match your current password.')],
            ]);
        }
    }
}
