<?php

namespace App\Services\Support;

use App\Data\Filtering\FilterDefinition;
use App\Data\Filtering\FilterDefinitionData;
use App\Data\Filtering\FilterModeOptionData;
use App\Enums\FilterMatchMode;
use App\Support\Filtering\FiltersByMatchMode;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

abstract class AbstractFilterableQueryService
{
    /**
     * @return array<int, FilterDefinition>
     */
    abstract protected function filterDefinitions(): array;

    abstract protected function baseQuery(): Builder;

    abstract protected function defaultSort(): string;

    /**
     * @return array<int, int>
     */
    protected function perPageOptions(): array
    {
        return [20, 50, 100];
    }

    /**
     * @return array<int, string>
     */
    protected function additionalSortableFields(): array
    {
        return [];
    }

    /**
     * @return array<int, FilterDefinitionData>
     */
    public function filterDefinitionData(): array
    {
        $items = [];

        foreach ($this->filterDefinitions() as $definition) {
            $modes = array_map(
                fn (FilterMatchMode $mode): FilterModeOptionData => new FilterModeOptionData(
                    label: $mode->label(),
                    value: $mode->value,
                ),
                $definition->allowedModes,
            );

            $items[] = new FilterDefinitionData(
                field: $definition->field,
                type: $definition->type,
                modes: $modes,
            );
        }

        return $items;
    }

    /**
     * @return array<string, array{type: string, modes: array<int, array{label: string, value: string}>}>
     */
    public function frontendFilterDefinitions(): array
    {
        $output = [];

        foreach ($this->filterDefinitionData() as $definition) {
            $modes = array_map(
                fn (FilterModeOptionData $mode): array => [
                    'label' => $mode->label,
                    'value' => $mode->value,
                ],
                $definition->modes,
            );

            $output[$definition->field] = [
                'type' => $definition->type,
                'modes' => $modes,
            ];
        }

        return $output;
    }

    public function paginate(Request $request): LengthAwarePaginator
    {
        $perPage = (int) $request->integer('perPage', $this->perPageOptions()[0]);

        if (!in_array($perPage, $this->perPageOptions(), true)) {
            $perPage = $this->perPageOptions()[0];
        }

        return QueryBuilder::for($this->baseQuery())
            ->allowedFilters($this->allowedFilters())
            ->allowedSorts($this->sortableFields())
            ->defaultSort($this->defaultSort())
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * @return array<int, string>
     */
    protected function sortableFields(): array
    {
        return collect($this->filterDefinitions())
            ->filter(fn (FilterDefinition $definition): bool => $definition->sortable)
            ->map(fn (FilterDefinition $definition): string => $definition->field)
            ->merge($this->additionalSortableFields())
            ->unique()
            ->values()
            ->all();
    }

    /**
     * @return array<int, AllowedFilter>
     */
    protected function allowedFilters(): array
    {
        return array_map(
            fn (FilterDefinition $definition): AllowedFilter => AllowedFilter::custom(
                $definition->field,
                new FiltersByMatchMode(
                    column: $definition->column,
                    type: $definition->type,
                    allowedModes: $definition->allowedModes,
                )
            ),
            $this->filterDefinitions(),
        );
    }
}
