<?php

/**
 * Transaction Number and Status formatter
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */

namespace Common\Service\Table\Formatter;

/**
 * Transaction Number and Status formatter
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class TransactionNoAndStatus implements FormatterInterface
{
    /**
     * Format a fee status
     *
     * @param array $row
     * @param array $column
     * @param \Laminas\ServiceManager\ServiceManager $serviceLocator
     * @return string
     * @inheritdoc
     */
    public static function format($row, $column = null, $serviceLocator = null)
    {

        $link = TransactionUrl::format($row, $column, $serviceLocator);

        $status = TransactionStatus::format($row, $column, $serviceLocator);

        return $link . ' ' . $status;
    }
}
