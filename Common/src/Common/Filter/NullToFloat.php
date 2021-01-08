<?php

namespace Common\Filter;

use Laminas\Filter\AbstractFilter;

/**
 * Class NullToFloat
 * @package Common\Filter
 */
class NullToFloat extends AbstractFilter
{
    /**
     * Filter
     *
     * @param mixed $value Value to check
     *
     * @return mixed
     */
    public function filter($value)
    {
        if ($value === false || empty($value)) {
            return 0;
        }

        return $value;
    }
}
