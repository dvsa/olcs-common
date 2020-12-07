<?php

/**
 * Fee Transaction Date formatter
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */

namespace Common\Service\Table\Formatter;

/**
 * Fee Transaction Date formatter
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class FeeTransactionDate implements FormatterInterface
{
    /**
     * @param array $data
     * @param array $column
     * @param \Laminas\ServiceManager\ServiceManager $sm
     * @return string
     */
    public static function format($data, $column = array(), $sm = null)
    {
        $value = StackValue::format($data, $column, $sm);

        $newData = [
            'value' => $value,
        ];
        $column['name'] = 'value';

        return Date::format($newData, $column, $sm);
    }
}
