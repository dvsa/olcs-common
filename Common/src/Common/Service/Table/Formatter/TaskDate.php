<?php

/**
 * task date formatter
 *
 * @author nick payne <nick.payne@valtech.co.uk>
 */

namespace Common\Service\Table\Formatter;

/**
 * task date formatter
 *
 * @author nick payne <nick.payne@valtech.co.uk>
 */
class TaskDate implements FormatterInterface
{
    /**
     * Format a task date
     *
     * @param array $data
     * @param array $column
     * @param \Zend\ServiceManager\ServiceManager $sm
     * @return string
     */
    public static function format($data, $column, $sm)
    {
        $date = Date::format($data, $column, $sm);
        if (isset($data['urgent']) && $data['urgent'] === 'Y') {
            // @TODO no AC for what the urgent marker looks like
            $date .= ' (urgent)';
        }
        return $date;
    }
}
