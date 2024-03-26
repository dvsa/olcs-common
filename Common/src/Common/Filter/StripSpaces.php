<?php

namespace Common\Filter;

use Laminas\Filter\AbstractFilter;

class StripSpaces extends AbstractFilter
{
    /**
     * Strip spaces
     *
     * @param string $value Value to strip spaces from
     *
     * @return string
     */
    public function filter($value)
    {
        return str_replace(' ', '', $value);
    }
}
