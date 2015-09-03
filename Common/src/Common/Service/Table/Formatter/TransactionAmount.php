<?php

/**
 * Transaction Amount formatter
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */

namespace Common\Service\Table\Formatter;

use Common\RefData;

/**
 * Transaction Amount formatter
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class TransactionAmount extends Money
{
    /**
     * Format a transaction amount
     *
     * @param array $data
     * @param array $column
     * @param \Zend\ServiceManager\ServiceManager $sm
     * @return string
     */
    public static function format($data, $column = array())
    {
        $class = '';

        if ($data['transaction']['status']['id'] !== RefData::TRANSACTION_STATUS_COMPLETE) {
            $class = ' class="void"';
        }

        $amount = parent::format($data, $column);

        return sprintf('<span%s>%s</span>', $class, $amount);
    }
}
