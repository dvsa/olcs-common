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
     * @param \Laminas\ServiceManager\ServiceManager $sm
     * @return string
     */
    public static function format($data, $column = array(), $sm = null)
    {
        if (!isset($column['dateformat'])) {
            $column['dateformat'] = \DATE_FORMAT;
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
