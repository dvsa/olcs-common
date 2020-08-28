<?php

namespace Common\Filter;

use Zend\Filter\AbstractFilter;

/**
 * Class NotPopulatedStringToZero
 * @package Common\Filter
 */
class NotPopulatedStringToZero extends AbstractFilter
{
    const ZERO = '0';

    /**
     * Filter
     *
     * @param mixed $value Value to check
     *
     * @return string
     */
    public function filter($value)
    {
        if (!is_string($value)) {
            return self::ZERO;
        }

        if ($value == '') {
            return self::ZERO;
        }

        return $value;
    }
}
