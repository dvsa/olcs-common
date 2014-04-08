<?php

/**
 * Date formatter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Common\Service\Table\Formatter;

/**
 * Date formatter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Date implements FormatterInterface
{
    /**
     * Format a date
     *
     * @param array $data
     * @param array $column
     * @return string
     */
    public static function format($data, $column)
    {
        if (!isset($column['dateformat'])) {
            $column['dateformat'] = 'd/m/Y';
        }

        if (isset($data[$column['name']]) && !is_null($data[$column['name']])) {
            return date($column['dateformat'], strtotime($data[$column['name']]));
        }

        return '';
    }
}