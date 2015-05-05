<?php

/**
 * Fee Amount formatter
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */

namespace Common\Service\Table\Formatter;

/**
 * Fee Amount formatter
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class FeeAmount implements FormatterInterface
{
    /**
     * Format a date
     *
     * @param array $data
     * @param array $column
     * @param \Zend\ServiceManager\ServiceManager $sm
     * @return string
     */
    public static function format($data, $column = array(), $sm = null)
    {
        unset($sm); // unused param

        if (isset($column['name']) && isset($data[$column['name']]) && !is_null($data[$column['name']])) {
            $amount = $data[$column['name']];
            return '£'.number_format($amount, 2);
        }

        return '';
    }
}
