<?php

namespace App\Services\Concerns;

use Carbon\CarbonImmutable;
use InvalidArgumentException;

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

    /**
     * @param  list<string>|null  $values
     * @return non-empty-list<int>|null
     */
    protected function integerListOrNull(?array $values): ?array
    {
        if ($values === null || $values === []) {
            return null;
        }

        return array_map(static function (string $value): int {
            $integer = filter_var($value, FILTER_VALIDATE_INT);

            if ($integer === false) {
                throw new InvalidArgumentException('Expected an integer-form string.');
            }

            return $integer;
        }, $values);
    }

    protected function carbonImmutableOrNull(?string $value): ?CarbonImmutable
    {
        $value = $this->trimmedStringOrNull($value);

        return $value === null ? null : CarbonImmutable::parse($value);
    }
}
