<?php

/**
 * Fee Number with Status formatter
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */

namespace Common\Service\Table\Formatter;

/**
 * Fee Number with Status formatter
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class FeeNoAndStatus implements FormatterPluginManagerInterface
{
    private FeeStatus $feeStatusFormatter;

    public function __construct(FeeStatus $feeStatusFormatter)
    {
        $this->feeStatusFormatter = $feeStatusFormatter;
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
        return $row['id'] . ' ' . $this->feeStatusFormatter->format($row);
    }
}
