<?php

/**
 * Name formatter
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */

namespace Common\Service\Table\Formatter;

/**
 * Name formatter
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class Name implements FormatterInterface
{

    /**
     * Format a name
     *
     * @param array $data
     * @param array $column
     * @param \Zend\ServiceManager\ServiceManager $sm
     * @return string
     */
    public static function format($data, $column = array(), $sm = null)
    {
        $title = !empty($data['title']['description']) ? $data['title']['description'] . ' ' : '';
        return $title . $data['forename'] . ' ' . $data['familyName'];
    }
}
