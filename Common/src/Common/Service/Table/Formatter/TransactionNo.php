<?php

/**
 * Transaction Number formatter
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */

namespace Common\Service\Table\Formatter;

use Common\RefData;

/**
 * Transaction Number formatter
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class TransactionNo implements FormatterInterface
{
    /**
     * Format a fee status
     *
     * @param array $row
     * @param array $column
     * @param \Zend\ServiceManager\ServiceManager $serviceLocator
     * @return string
     */
    public static function format($row, $column = null, $serviceLocator = null)
    {
        $statusClass = 'status';
        switch ($row['transaction']['status']['id']) {
            case RefData::TRANSACTION_STATUS_FAILED:
            case RefData::TRANSACTION_STATUS_CANCELLED:
                $statusClass .= ' red';
                break;
            case RefData::TRANSACTION_STATUS_PAID:
            case RefData::TRANSACTION_STATUS_COMPLETE:
                $statusClass .= ' green';
                break;
            case RefData::TRANSACTION_STATUS_OUTSTANDING:
                $statusClass .= ' orange';
                break;
            default:
                $statusClass .= ' grey';
                break;
        }
        return vsprintf(
            '%s <span class="%s">%s</span>',
            [$row['transaction']['id'], $statusClass, $row['transaction']['status']['description']]
        );
    }
}
