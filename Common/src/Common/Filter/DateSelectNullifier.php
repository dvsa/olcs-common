<?php

namespace Common\Filter;

use Zend\Filter\AbstractFilter;
use Zend\Filter\Exception;

/**
 * Class DateSelectNullifier
 * @package Common\Filter
 */
class DateSelectNullifier extends AbstractFilter
{
    /**
     * Returns the result of filtering $value
     *
     * @param  mixed $date
     * @return mixed
     */
    public function filter($date)
    {
        if (!is_array($date) || empty($date['year']) || empty($date['month']) || empty($date['day'])) {
            return null;
        }

        return $date['year'] . '-' . $date['month'] . '-' . $date['day'];
    }
}
