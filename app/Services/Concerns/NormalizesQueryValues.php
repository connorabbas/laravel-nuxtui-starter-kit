<?php

namespace App\Services\Concerns;

trait NormalizesQueryValues
{
    protected function trimmedStringOrNull(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim($value);

        return $value === '' ? null : $value;
    }
}
