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
class TransactionNoAndStatus implements FormatterPluginManagerInterface
{
    protected TransactionUrl $transactionUrlFormatter;

    public function __construct(TransactionUrl $transactionUrlFormatter, TransactionStatus $transactionStatusFormatter)
    {
            $this->transactionUrlFormatter = $transactionUrlFormatter;
            $this->transactionStatusFormatter = $transactionStatusFormatter;
    }
    /**
     * Format a fee status
     *
     * @param      array $row
     * @param      array $column
     * @return     string
     * @inheritdoc
     */
    public function format($row, $column = null)
    {

        $link = TransactionUrl::format($row, $column);

        $status = TransactionStatus::format($row, $column);

        return $link . ' ' . $status;
    }
}
