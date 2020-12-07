<?php

/**
 * Sum Columns formatter
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */

namespace Common\Service\Table\Formatter;

/**
 * Sum Columns formatter
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class SumColumns implements FormatterInterface
{
    /**
     * Sums the data of a specific columns
     *
     * @param array $data
     * @param array $column
     * @param \Laminas\ServiceManager\ServiceManager $sm
     * @return string
     */
    public static function format($data, $column = array(), $sm = null)
    {
        $total = 0;
        if (isset($column['columns']) && is_array($column['columns'])) {
            foreach ($column['columns'] as $name) {
                if (isset($data[$name]) && is_numeric($data[$name])) {
                    $total += (float)$data[$name];
                }
            }
        }

        return (string)$total;
    }
}
