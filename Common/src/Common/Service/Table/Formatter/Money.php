<?php

/**
 * Money formatter
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */

namespace Common\Service\Table\Formatter;

/**
 * Money formatter
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class Money implements FormatterInterface
{
    /**
     * Format a fee amount
     *
     * @param array $data
     * @param array $column
     * @param \Laminas\ServiceManager\ServiceManager $sm
     * @return string
     */
    public static function format($data, $column = array())
    {
        if (isset($column['name']) && isset($data[$column['name']])) {
            $amount = $data[$column['name']];
            return '£'.number_format($amount, 2);
        }

        return '';
    }
}
