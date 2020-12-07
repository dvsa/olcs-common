<?php

namespace Common\Service\Table\Formatter;

/**
 * Translate formatter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Translate implements FormatterInterface
{
    /**
     * Translate value
     *
     * @param array                               $data   Data
     * @param array                               $column Column parameters
     * @param \Laminas\ServiceManager\ServiceManager $sm     Service manager
     *
     * @return string
     */
    public static function format($data, $column = array(), $sm = null)
    {
        if (isset($column['name'])) {
            return $sm->get('translator')->translate(
                $sm->get('Helper\Data')
                    ->fetchNestedData($data, $column['name'])
            );
        }

        if (isset($column['content'])) {
            return $sm->get('translator')->translate($column['content']);
        }

        return '';
    }
}
