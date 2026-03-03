<?php

namespace App\Services\Examples;

use App\Data\Filtering\FilterDefinition;
use App\Data\Filtering\FilterModeOptionData;
use App\Enums\FilterMatchMode;
use App\Models\Account;
use App\Models\User;
use App\Services\Support\AbstractFilterableQueryService;
use Illuminate\Database\Eloquent\Builder;

class UserDirectoryQueryService extends AbstractFilterableQueryService
{
    protected function baseQuery(): Builder
    {
        return User::query()
            ->with([
                'accounts:id,user_id,name,provider,status,balance,opened_at',
            ])
            ->withCount('accounts')
            ->withSum('accounts', 'balance')
            ->withMax('accounts', 'opened_at');
    }

    protected function defaultSort(): string
    {
        return '-created_at';
    }

    /**
     * @return array<int, FilterDefinition>
     */
    protected function filterDefinitions(): array
    {
        return [
            FilterDefinition::make('id', 'number', [
                FilterMatchMode::EQUALS,
                FilterMatchMode::NOT_EQUALS,
                FilterMatchMode::LESS_THAN,
                FilterMatchMode::LESS_THAN_OR_EQUAL_TO,
                FilterMatchMode::GREATER_THAN,
                FilterMatchMode::GREATER_THAN_OR_EQUAL_TO,
                FilterMatchMode::BETWEEN,
            ]),
            FilterDefinition::make('name', 'string', [
                FilterMatchMode::STARTS_WITH,
                FilterMatchMode::CONTAINS,
                FilterMatchMode::NOT_CONTAINS,
                FilterMatchMode::ENDS_WITH,
                FilterMatchMode::EQUALS,
                FilterMatchMode::NOT_EQUALS,
            ]),
            FilterDefinition::make('email', 'string', [
                FilterMatchMode::STARTS_WITH,
                FilterMatchMode::CONTAINS,
                FilterMatchMode::NOT_CONTAINS,
                FilterMatchMode::ENDS_WITH,
                FilterMatchMode::EQUALS,
                FilterMatchMode::NOT_EQUALS,
            ]),
            FilterDefinition::make('created_at', 'date', [
                FilterMatchMode::DATE_IS,
                FilterMatchMode::DATE_IS_NOT,
                FilterMatchMode::DATE_BEFORE,
                FilterMatchMode::DATE_AFTER,
                FilterMatchMode::BETWEEN,
            ]),
            FilterDefinition::make('accounts_status', 'string', [
                FilterMatchMode::EQUALS,
            ], column: 'accounts.status', sortable: false),
            FilterDefinition::make('accounts_provider', 'string', [
                FilterMatchMode::IN,
            ], column: 'accounts.provider', sortable: false),
            FilterDefinition::make('accounts_opened_at', 'date', [
                FilterMatchMode::DATE_IS,
                FilterMatchMode::DATE_IS_NOT,
                FilterMatchMode::DATE_BEFORE,
                FilterMatchMode::DATE_AFTER,
                FilterMatchMode::BETWEEN,
            ], column: 'accounts.opened_at', sortable: false),
        ];
    }

    /**
     * @return array<int, string>
     */
    protected function additionalSortableFields(): array
    {
        return [
            'accounts_count',
            'accounts_sum_balance',
            'accounts_max_opened_at',
        ];
    }

    /**
     * @return array<int, FilterModeOptionData>
     */
    public function accountStatusOptions(): array
    {
        return [
            new FilterModeOptionData(label: 'Active', value: 'active'),
            new FilterModeOptionData(label: 'Suspended', value: 'suspended'),
            new FilterModeOptionData(label: 'Closed', value: 'closed'),
        ];
    }

    /**
     * @return array<int, FilterModeOptionData>
     */
    public function accountProviderOptions(): array
    {
        $providers = Account::query()
            ->whereNotNull('provider')
            ->select('provider')
            ->distinct()
            ->orderBy('provider')
            ->pluck('provider')
            ->all();

        return array_map(
            fn (string $provider): FilterModeOptionData => new FilterModeOptionData(
                label: $provider,
                value: $provider,
            ),
            $providers,
        );
    }

    /**
     * @return array<int, array{label: string, value: string}>
     */
    public function accountStatusOptionsFrontend(): array
    {
        return array_map(
            fn (FilterModeOptionData $option): array => [
                'label' => $option->label,
                'value' => $option->value,
            ],
            $this->accountStatusOptions(),
        );
    }

    /**
     * @return array<int, array{label: string, value: string}>
     */
    public function accountProviderOptionsFrontend(): array
    {
        return array_map(
            fn (FilterModeOptionData $option): array => [
                'label' => $option->label,
                'value' => $option->value,
            ],
            $this->accountProviderOptions(),
        );
    }
}
