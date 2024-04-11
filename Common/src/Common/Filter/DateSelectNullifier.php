<?php

namespace Common\Filter;

use Laminas\Filter\AbstractFilter;

/**
 * Class DateSelectNullifier
 * @package Common\Filter
 */
class DateSelectNullifier extends AbstractFilter
{
    /**
     * Returns the result of filtering $value
     *
     * @param  array|string $value Date
     *
     * @return string|null
     */
    public function filter($value): ?string
    {
        if (empty($value)) {
            return null;
        }
        if (is_string($value)) {
            return $value;
        }
        if (!is_array($value) || empty($value['year']) || empty($value['month']) || empty($value['day'])) {
            return null;
        }

        return $value['year'] . '-' . $value['month'] . '-' . $value['day'];
    }
}
