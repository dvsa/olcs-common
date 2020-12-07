<?php

/**
 * Fee Amount Sum formatter
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Common\Service\Table\Formatter;

/**
 * Fee Amount Sum formatter
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class FeeAmountSum implements FormatterInterface
{
    /**
     * Sums the data of a specific column and formats the result as a fee amount
     *
     * @param array $data
     * @param array $column
     * @param \Laminas\ServiceManager\ServiceManager $sm
     * @return string
     */
    public static function format($data, $column = array(), $sm = null)
    {
        if (isset($column['name'])) {
            $data[$column['name']] = Sum::format($data, $column, $sm);
            return FeeAmount::format($data, $column, $sm);
        }
    }
}
