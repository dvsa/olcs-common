<?php

/**
 * Transaction Amount Sum formatter
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Common\Service\Table\Formatter;

use Common\RefData;

/**
 * Transaction Amount Sum formatter
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class TransactionAmountSum implements FormatterInterface
{
    /**
     * Sums the data of a specific column and formats the result as a fee amount
     *
     * @param array $data
     * @param array $column
     * @param \Zend\ServiceManager\ServiceManager $sm
     * @return string
     */
    public static function format($data, $column = array(), $sm = null)
    {
        $sum = 0;

        foreach ($data as $row) {
            if ($row['transaction']['status']['id'] === RefData::TRANSACTION_STATUS_COMPLETE) {
                $sum += (float)$row['amount'];
            }
        }

        $data[$column['name']] = $sum;
        return Money::format($data, $column, $sm);
    }
}
