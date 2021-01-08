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
     * @param array $data   data row
     * @param array $column column specification
     * @param \Laminas\ServiceManager\ServiceManager $sm
     * @return string
     */
    public static function format($data, $column = array(), $sm = null)
    {
        // if column[name] is specified, look within that index for the data
        if (isset($column['name'])) {
            // if column[name] contains "->" look deeper
            if (strpos($column['name'], '->')) {
                $data = $sm->get('Helper\Data')->fetchNestedData($data, $column['name']);
            } elseif (isset($data[$column['name']])) {
                $data = $data[$column['name']];
            }
        }

        $title = !empty($data['title']['description']) ? $data['title']['description'] . ' ' : '';
        return $title . $data['forename'] . ' ' . $data['familyName'];
    }
}
