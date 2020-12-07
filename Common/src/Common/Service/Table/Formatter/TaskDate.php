<?php

/**
 * Task date formatter
 *
 * @author Nick payne <nick.payne@valtech.co.uk>
 */

namespace Common\Service\Table\Formatter;

/**
 * Task date formatter
 *
 * @author Nick payne <nick.payne@valtech.co.uk>
 */
class TaskDate implements FormatterInterface
{
    /**
     * Format a task date
     *
     * @param array $data
     * @param array $column
     * @param \Laminas\ServiceManager\ServiceManager $sm
     * @return string
     */
    public static function format($data, $column = array(), $sm = null)
    {
        $date = Date::format($data, $column, $sm);
        if (isset($data['urgent']) && $data['urgent'] === 'Y') {
            // @TODO no AC for what the urgent marker looks like
            // $date .= ' <span class="status orange">urgent</span>';
            $date .= ' (urgent)';
        }

        if (isset($data['isClosed']) && $data['isClosed'] === 'Y') {
            $date .= ' <span class="status red">closed</span>';
        }

        return $date;
    }
}
