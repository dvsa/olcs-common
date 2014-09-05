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
class DateTime implements FormatterInterface
{
    /**
     * Format a date and time
     *
     * @param array $data
     * @param array $column
     * @param \Zend\ServiceManager\ServiceManager $sm
     * @return string
     */
    public static function format($data, $column = array(), $sm = null)
    {
        if (!isset($column['dateformat'])) {
            $column['dateformat'] = 'd/m/Y HH:MM';
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
