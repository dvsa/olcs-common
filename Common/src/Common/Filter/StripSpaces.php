<?php

namespace Common\Filter;

use Laminas\Filter\AbstractFilter;

/**
 * Class StripSpaces
 * @package Common\Filter
 *
 * @template-extends AbstractFilter<string, string>
 */
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
