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
     * @param \Zend\ServiceManager\ServiceManager $sm
     * @return string
     */
    public static function format($data, $column = array(), $sm = null)
    {
        $sum = 0;

        if (isset($column['name'])) {
            foreach ($data as $row) {
                if (isset($row[$column['name']]) && is_numeric($row[$column['name']])) {
                    $sum += (int)$row[$column['name']];
                }
            }
        }

        return (string)$sum;
    }
}
