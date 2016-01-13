<?php

/**
 * Hide If Closed Radio formatter
 */

namespace Common\Service\Table\Formatter;

/**
 * Hide If Closed Radio formatter
 *
 */
class HideIfClosedRadio implements FormatterInterface
{
    /**
     * Format a radio
     *
     * @param array $data
     * @param array $column
     * @param \Zend\ServiceManager\ServiceManager $sm
     * @return string
     */
    public static function format($data, $column = array(), $sm = null)
    {
        if (!empty($data['closedDate'])) {
            return '';
        }

        return '<input type="radio" value="'.$data['id'].'" name="id">';
    }
}
