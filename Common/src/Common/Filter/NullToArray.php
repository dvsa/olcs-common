<?php

namespace Common\Filter;

use Zend\Filter\AbstractFilter;
use Zend\Filter\FilterInterface;
use Zend\InputFilter\Input as ZendInput;

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
