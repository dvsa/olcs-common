<?php

namespace Common\Service\Table\Formatter;

/**
 * Fee Amount Sum formatter
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class FeeAmountSum implements FormatterPluginManagerInterface
{
    private Sum $sumFormatter;
    private FeeAmount $feeAmountFormatter;

    /**
     * @param Sum       $sumFormatter
     * @param FeeAmount $feeAmountFormatter
     */
    public function __construct(Sum $sumFormatter, FeeAmount $feeAmountFormatter)
    {
        $this->sumFormatter = $sumFormatter;
        $this->feeAmountFormatter = $feeAmountFormatter;
    }

    /**
     * Sums the data of a specific column and formats the result as a fee amount
     *
     * @param  array $data
     * @param  array $column
     * @return string
     */
    public function format($data, $column = [])
    {
        if (isset($column['name'])) {
            $data[$column['name']] = $this->sumFormatter->format($data, $column);
            return $this->feeAmountFormatter->format($data, $column);
        }
    }
}
