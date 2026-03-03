<?php

namespace App\Support\Filtering;

use App\Enums\FilterMatchMode;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class FiltersByMatchMode implements Filter
{
    /**
     * @param  array<FilterMatchMode>  $allowedModes
     */
    public function __construct(
        private readonly string $column,
        private readonly string $type,
        private readonly array $allowedModes,
    ) {
    }

    public function __invoke(Builder $query, mixed $value, string $property): void
    {
        if (!is_array($value)) {
            return;
        }

        $mode = FilterMatchMode::tryFrom((string) ($value['op'] ?? ''));
        if (!$mode || !in_array($mode, $this->allowedModes, true)) {
            return;
        }

        $normalizedValue = $this->normalizeValue($value['value'] ?? null, $mode);
        if ($normalizedValue === null) {
            return;
        }

        if (str_contains($this->column, '.')) {
            [$relation, $column] = $this->parseRelationColumn();

            $query->whereHas($relation, function (Builder $relationQuery) use ($mode, $normalizedValue, $column): void {
                $this->applyMatchModeQuery($relationQuery, $mode, $normalizedValue, $column);
            });

            return;
        }

        $this->applyMatchModeQuery($query, $mode, $normalizedValue, $this->column);
    }

    private function applyMatchModeQuery(
        Builder $query,
        FilterMatchMode $mode,
        string|int|float|bool|array $normalizedValue,
        string $column,
    ): void {

        match ($mode) {
            FilterMatchMode::STARTS_WITH => $query->whereLike($column, "{$normalizedValue}%"),
            FilterMatchMode::CONTAINS => $query->whereLike($column, "%{$normalizedValue}%"),
            FilterMatchMode::NOT_CONTAINS => $query->whereNotLike($column, "%{$normalizedValue}%"),
            FilterMatchMode::ENDS_WITH => $query->whereLike($column, "%{$normalizedValue}"),
            FilterMatchMode::EQUALS => $query->where($column, '=', $normalizedValue),
            FilterMatchMode::NOT_EQUALS => $query->where($column, '!=', $normalizedValue),
            FilterMatchMode::IN => $query->whereIn($column, $normalizedValue),
            FilterMatchMode::LESS_THAN => $query->where($column, '<', $normalizedValue),
            FilterMatchMode::LESS_THAN_OR_EQUAL_TO => $query->where($column, '<=', $normalizedValue),
            FilterMatchMode::GREATER_THAN => $query->where($column, '>', $normalizedValue),
            FilterMatchMode::GREATER_THAN_OR_EQUAL_TO => $query->where($column, '>=', $normalizedValue),
            FilterMatchMode::BETWEEN => $this->applyBetween($query, $normalizedValue, $column),
            FilterMatchMode::DATE_IS => $query->whereDate($column, '=', $normalizedValue),
            FilterMatchMode::DATE_IS_NOT => $query->whereDate($column, '!=', $normalizedValue),
            FilterMatchMode::DATE_BEFORE => $query->whereDate($column, '<', $normalizedValue),
            FilterMatchMode::DATE_AFTER => $query->whereDate($column, '>', $normalizedValue),
        };
    }

    /**
     * @return array{string, string}
     */
    private function parseRelationColumn(): array
    {
        $segments = explode('.', $this->column);
        $column = (string) array_pop($segments);
        $relation = implode('.', $segments);

        return [$relation, $column];
    }

    private function applyBetween(Builder $query, array $value, string $column): void
    {
        if (count($value) !== 2) {
            return;
        }

        [$start, $end] = $value;
        if ($this->type === 'date') {
            $query
                ->whereDate($column, '>=', (string) $start)
                ->whereDate($column, '<=', (string) $end);

            return;
        }

        $query->whereBetween($column, [$start, $end]);
    }

    private function normalizeValue(mixed $rawValue, FilterMatchMode $mode): string|int|float|bool|array|null
    {
        if (in_array($mode, [FilterMatchMode::IN, FilterMatchMode::BETWEEN], true)) {
            if (!is_array($rawValue) || count($rawValue) === 0) {
                return null;
            }

            $normalized = array_values(array_filter(
                array_map(fn (mixed $value): string|int|float|bool|null => $this->normalizeScalar($value), $rawValue),
                fn (mixed $value): bool => $value !== null
            ));

            if (count($normalized) === 0) {
                return null;
            }

            return $normalized;
        }

        return $this->normalizeScalar($rawValue);
    }

    private function normalizeScalar(mixed $value): string|int|float|bool|null
    {
        if ($value === null || $value === '') {
            return null;
        }

        if ($this->type === 'date') {
            try {
                return Carbon::parse((string) $value)->toDateString();
            } catch (\Throwable) {
                return null;
            }
        }

        if ($this->type === 'number') {
            if (!is_numeric($value)) {
                return null;
            }

            if (str_contains((string) $value, '.')) {
                return (float) $value;
            }

            return (int) $value;
        }

        if ($this->type === 'boolean') {
            return filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        }

        return (string) $value;
    }
}
