<?php

namespace Common\Service\Table\Formatter;

/**
 * Date formatter with fix for Timezone Issues
 *
 * Required due to the fact that the current Date Formatter will add 1 to the day if the time set in the Date object is
 * greater than 2200. This was due to the Date Object it was using.
 *
 * @author Scott Callaway <scott.callaway@capgemini.com>
 */
class DateTimezoneFix implements FormatterInterface
{
    /**
     * Format a date
     *
     * @param array $data
     * @param array $column
     *
     * @return string
     */
    public static function format($data, $column = array())
    {
        if (!isset($column['dateformat'])) {
            $column['dateformat'] = \DATE_FORMAT;
        }

        if (isset($data[$column['name']]) &&
            !is_null($data[$column['name']]) &&
            $data[$column['name']] !== ''
        ) {
            $date = new \DateTime($data[$column['name']]);
            return $date->format($column['dateformat']);
        }

        return '';
    }
}
