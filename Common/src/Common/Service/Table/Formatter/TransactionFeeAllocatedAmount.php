<?php

/**
 * Transaction Fee Allocated Amount formatter
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */

namespace Common\Service\Table\Formatter;

/**
 * Transaction Fee Allocated Amount formatter
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class TransactionFeeAllocatedAmount extends Money
{
    /**
     * Format a transaction fee allocated amount
     *
     * @param array $data
     * @param array $column
     * @param \Laminas\ServiceManager\ServiceManager $sm
     * @return string
     */
    public static function format($data, $column = array())
    {
        $amount = parent::format($data, $column);

        if (isset($data['reversingTransaction'])) {
            return sprintf('<span class="void">%s</span>', $amount);
        }

        return $amount;
    }
}
