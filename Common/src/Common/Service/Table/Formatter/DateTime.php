<?php

/**
 * Date and time formatter
 *
 * @author Jessica Rowbottom <jess.rowbottom@valtech.co.uk>
 */

namespace Common\Service\Table\Formatter;

/**
 * Date and time formatter
 *
 * @author Jessica Rowbottom <jess.rowbottom@valtech.co.uk>
 */
class DateTime implements FormatterPluginManagerInterface
{
    /**
     * Format a date and time
     *
     * @param  array $data
     * @param  array $column
     * @return string
     */
    public function format($data, $column = [])
    {
        if (!isset($column['dateformat'])) {
            $column['dateformat'] = \DATETIME_FORMAT;
        }

        if (isset($data[$column['name']]) && !is_null($data[$column['name']])) {
            $date = $data[$column['name']];

            if (is_array($date) && isset($date['date'])) {
                $date = $date['date'];
            }

            return date($column['dateformat'], strtotime($date));
        }

        return '';
    }
}
