<?php

namespace Common\Service\Table\Formatter;

use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * YesNo formatter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class YesNo implements FormatterInterface
{
    /**
     * Format a address
     *
     * @param array                   $data   Data
     * @param array                   $column Column parameterd
     * @param ServiceLocatorInterface $sm     Service Manager
     *
     * @return string
     */
    public static function format($data, array $column = [], ServiceLocatorInterface $sm = null)
    {
        if (isset($column['stack'])) {
            if (is_string($column['stack'])) {
                $column['stack'] = explode('->', $column['stack']);
            }

            $value = $sm->get('Helper\Stack')->getStackValue($data, $column['stack']);
        } else {
            $value = $data[$column['name']];
        }

        return $sm->get('translator')->translate(
            $value !== 'N' && !empty($value)
            ? 'common.table.Yes'
            : 'common.table.No'
        );
    }
}
