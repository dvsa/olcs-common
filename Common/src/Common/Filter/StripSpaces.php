<?php

namespace Common\Filter;

use Laminas\Filter\AbstractFilter;

/**
 * Class StripSpaces
 * @package Common\Filter
 */
class StripSpaces extends AbstractFilter
{
    /**
     * Strip spaces
     *
     * @param string|array $value Value to strip spaces from
     *
     * @return string|array
     */
    public function filter($value)
    {
        if (!is_scalar($value) && !is_array($value)) {
            return $value;
        }
        return str_replace(' ', '', $value);
    }
}
