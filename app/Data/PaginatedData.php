<?php

namespace App\Data;

use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class PaginatedData extends Data
{
    public function __construct(
        #[Min(1)]
        public int $page = 1,
        #[Min(1), Max(100)]
        public int $perPage = 10,
    ) {
    }
}
