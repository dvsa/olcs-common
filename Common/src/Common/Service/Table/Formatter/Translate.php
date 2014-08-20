<?php

/**
 * Translate formatter
 *
 * @author Jakub Igla <jakub.igla@valtech.co.uk>
 */

namespace Common\Service\Table\Formatter;

/**
 * Translate formatter
 *
 * @author Jakub Igla <jakub.igla@valtech.co.uk>
 */
class Translate implements FormatterInterface
{
    /**
     * Translate value
     *
     * @param array $data
     * @param array $column
     * @param \Zend\ServiceManager\ServiceManager $sm
     * @return string
     */
    public static function format($data, $column = array(), $sm = null)
    {
        if (isset($column['name'])) {
            return $sm->get('translator')->translate($data[$column['name']]);
        }

        if (isset($column['content'])) {
            return $sm->get('translator')->translate($column['content']);
        }

        return '';
    }
}
