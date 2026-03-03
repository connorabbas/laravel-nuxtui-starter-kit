<?php

namespace App\Data\Filtering;

use Spatie\LaravelData\Data;

class FilterModeOptionData extends Data
{
    public function __construct(
        public string $label,
        public string $value,
    ) {
    }
}
