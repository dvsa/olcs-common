<?php

namespace Common\Filter;

use Zend\Filter\AbstractFilter;
use Zend\Filter\FilterInterface;
use Zend\InputFilter\Input as ZendInput;

/**
 * Class NullToFloat
 * @package Common\Filter
 */
class NullToFloat extends AbstractFilter
{
    /**
     * @param mixed $value
     *
     * @return int|mixed
     */
    public function filter($value)
    {
        if ($value === null || $value === '') {
            return (float)0;
        }

        return $value;
    }
}
