<?php

namespace Common\Filter;

use Zend\Filter\AbstractFilter;

/**
 * Class DateSelectNullifier
 * @package Common\Filter
 */
class DateSelectNullifier extends AbstractFilter
{
    /**
     * Returns the result of filtering $value
     *
     * @param  array|string $date Date
     *
     * @return mixed
     */
    public function filter($date)
    {
        if (empty($date)) {
            return null;
        } elseif (is_string($date)) {
            return $date;
        } elseif (!is_array($date) || empty($date['year']) || empty($date['month']) || empty($date['day'])) {
            return null;
        }

        return $date['year'] . '-' . $date['month'] . '-' . $date['day'];
    }
}
