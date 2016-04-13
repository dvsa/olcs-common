<?php

/**
 * PI Hearing Status formatter
 */

namespace Common\Service\Table\Formatter;

/**
 * PI Hearing Status formatter
 */
class PiHearingStatus implements FormatterInterface
{
    /**
     * Format a PI Hearing status
     *
     * @param array $row
     * @return string
     */
    public static function format($row)
    {
        if (!empty($row['isCancelled']) && ($row['isCancelled'] === 'Y')) {
            $class = 'red';
            $text = 'CNL';
        } elseif (!empty($row['isAdjourned']) && ($row['isAdjourned'] === 'Y')) {
            $class = 'orange';
            $text = 'ADJ';
        }

        return isset($text) ? sprintf('<span class="status %s">%s</span>', $class, $text) : '';
    }
}
