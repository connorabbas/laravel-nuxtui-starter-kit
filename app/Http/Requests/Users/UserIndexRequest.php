<?php

namespace App\Http\Requests\Users;

use App\Data\Users\UserIndexQueryData;
use App\Enums\UserSort;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserIndexRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        if (! $this->has('userIds') || ! is_array($this->query('userIds'))) {
            return;
        }

        $userIds = array_values(array_filter(
            $this->query('userIds'),
            fn (mixed $userId): bool => $userId !== null && $userId !== '',
        ));

        $this->merge([
            'userIds' => $userIds === [] ? null : $userIds,
        ]);
    }

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
            'userIds' => ['nullable', 'array'],
            'userIds.*' => ['integer', Rule::exists(User::class, 'id')],
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

        if ($this->has('userIds')) {
            $userIds = array_map('intval', $validated['userIds'] ?? []);

            $validated['userIds'] = $userIds === [] ? null : $userIds;
        }

        return UserIndexQueryData::from($validated);
    }
}
