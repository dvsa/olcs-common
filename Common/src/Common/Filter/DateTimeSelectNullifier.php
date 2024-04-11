<?php

namespace Common\Filter;

use Laminas\Filter\AbstractFilter;
use Laminas\Filter\Exception;

/**
 * Class DateTimeSelectNullifier
 * @package Common\Filter
 */
class DateTimeSelectNullifier extends AbstractFilter
{
    /**
     * Returns the result of filtering $value
     *
     * @param  array $value
     *
     * @return string|null
     */
    public function filter($value)
    {
        if (
            !is_array($value)
            || (
                empty($value['year']) && empty($value['month']) && empty($value['day'])
                && empty($value['hour']) && empty($value['minute'])
            )
        ) {
            return null;
        }

        return $value['year'] . '-' . $value['month'] . '-' . $value['day'] . ' '
        . $value['hour'] . ':' . $value['minute'] . ':00';
    }
}
