<?php

namespace App\Data\Filtering;

use App\Enums\FilterMatchMode;
use Spatie\LaravelData\Data;

class FilterDefinition extends Data
{
    /**
     * @param  array<FilterMatchMode>  $allowedModes
     */
    public function __construct(
        public readonly string $field,
        public readonly string $column,
        public readonly string $type,
        public readonly array $allowedModes,
        public readonly bool $sortable = true,
    ) {
    }

    /**
     * @param  array<FilterMatchMode>  $allowedModes
     */
    public static function make(
        string $field,
        string $type,
        array $allowedModes,
        ?string $column = null,
        bool $sortable = true,
    ): self {
        return new self(
            field: $field,
            column: $column ?? $field,
            type: $type,
            allowedModes: $allowedModes,
            sortable: $sortable,
        );
    }
}
