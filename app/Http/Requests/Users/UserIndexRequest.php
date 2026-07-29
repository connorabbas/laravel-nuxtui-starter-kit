<?php

namespace App\Http\Requests\Users;

use App\Data\Users\UserIndexQueryData;
use App\Enums\UserSort;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserIndexRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'page' => ['integer', 'min:1'],
            'perPage' => ['integer', Rule::in([10, 25, 50])],
            'search' => ['nullable', 'string', 'max:100'],
            'verified' => ['nullable', 'boolean'],
            'createdFrom' => ['nullable', 'date_format:Y-m-d'],
            'createdUntil' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:createdFrom'],
            'sort' => [Rule::enum(UserSort::class)],
        ];
    }

    public function queryData(): UserIndexQueryData
    {
        $validated = $this->validated();

        if ($this->has('verified')) {
            $validated['verified'] = $this->boolean('verified');
        }

        return UserIndexQueryData::from($validated);
    }
}
