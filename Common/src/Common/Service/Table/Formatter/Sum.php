<?php

/**
 * Sum formatter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Common\Service\Table\Formatter;

/**
 * Sum formatter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Sum implements FormatterInterface
{
    /**
     * Sums the data of a specific column
     *
     * @param array $data
     * @param array $column
     * @param \Laminas\ServiceManager\ServiceManager $sm
     * @return string
     */
    public static function format($data, $column = array())
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
