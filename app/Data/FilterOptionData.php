<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class FilterOptionData extends Data
{
    public function __construct(
        public string|int|bool|null $value,
        public string $label,
    ) {
    }
}
