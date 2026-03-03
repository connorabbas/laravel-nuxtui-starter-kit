<?php

namespace App\Data\Filtering;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;

class FilterDefinitionData extends Data
{
    /**
     * @param  array<int, FilterModeOptionData>  $modes
     */
    public function __construct(
        public string $field,
        public string $type,
        #[DataCollectionOf(FilterModeOptionData::class)]
        public array $modes,
    ) {
    }
}
