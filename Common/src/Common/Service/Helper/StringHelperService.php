<?php

/**
 * String Helper Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Helper;

use Zend\Filter\Word\DashToCamelCase;
use Zend\Filter\Word\CamelCaseToDash;

/**
 * String Helper Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class StringHelperService extends AbstractHelperService
{
    /**
     * Convert dash to camel case
     *
     * @param string $string
     * @return string
     */
    public function dashToCamel($string)
    {
        $converter = new DashToCamelCase();
        return $converter->filter($string);
    }

    /**
     * Convert camel case to dash
     *
     * @param string $string
     * @return string
     */
    public function camelToDash($string)
    {
        $converter = new CamelCaseToDash();
        return strtolower($converter->filter($string));
    }
}
