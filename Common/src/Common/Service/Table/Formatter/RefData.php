<?php

namespace Common\Service\Table\Formatter;

use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * RefData formatter
 */
class RefData implements FormatterInterface
{
    /**
     * Format a address
     *
     * @param array                   $data   Row data
     * @param array                   $column Column params
     * @param ServiceLocatorInterface $sm     Service Manager
     *
     * @return string
     */
    public static function format($data, array $column = [], ServiceLocatorInterface $sm = null)
    {
        return $sm->get('translator')->translate($data[$column['name']]['description']);
    }
}
