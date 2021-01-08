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
     * @param  array $date
     *
     * @return string|null
     */
    public function filter($date)
    {
        if (!is_array($date)
            || (
                empty($date['year']) && empty($date['month']) && empty($date['day'])
                && empty($date['hour']) && empty($date['minute'])
            )
        ) {
            return null;
        }

        return $date['year'] . '-' . $date['month'] . '-' . $date['day'] . ' '
        . $date['hour']. ':' . $date['minute']. ':00';
    }
}
