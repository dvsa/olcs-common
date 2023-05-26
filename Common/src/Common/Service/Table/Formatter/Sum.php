<?php

namespace Common\Service\Table\Formatter;

/**
 * Sum formatter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Sum implements FormatterPluginManagerInterface
{
    /**
     * Sums the data of a specific column
     *
     * @param  array $data
     * @param  array $column
     * @return string
     */
    public function format($data, $column = [])
    {
        $sum = 0;

        if (isset($column['name'])) {
            foreach ($data as $row) {
                if (isset($row[$column['name']]) && is_numeric($row[$column['name']])) {
                    $sum += (float)$row[$column['name']];
                }
            }
        }

        return (string)$sum;
    }
}
