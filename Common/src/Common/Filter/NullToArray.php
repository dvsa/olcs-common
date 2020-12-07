<?php

namespace Common\Filter;

use Laminas\Filter\AbstractFilter;

/**
 * Class NullToArray
 * @package Common\Filter
 */
class NullToArray extends AbstractFilter
{
    /**
     * Filter
     *
     * @param mixed $value Value to check
     *
     * @return []
     */
    public function filter($value)
    {
        if (is_null($value)) {
            return [];
        }

        return $value;
    }
}
