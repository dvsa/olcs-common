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
class FeeTransactionDate implements FormatterPluginManagerInterface
{
    private StackValue $stackValueFormatter;

    private Date $dateFormatter;

    public function __construct(StackValue $stackValueFormatter, Date $dateFormatter)
    {
        $this->stackValueFormatter = $stackValueFormatter;
        $this->dateFormatter = $dateFormatter;
    }

    /**
     * @param  array $data
     * @param  array $column
     * @return string
     */
    public function format($data, $column = [])
    {
        $value = $this->stackValueFormatter->format($data, $column);

        $newData = [
            'value' => $value,
        ];
        $column['name'] = 'value';

        return $this->dateFormatter->format($newData, $column);
    }
}
