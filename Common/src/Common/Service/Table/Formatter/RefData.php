<?php

namespace Common\Service\Table\Formatter;

use Laminas\ServiceManager\ServiceLocatorInterface;

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
        $colData = $data[$column['name']];
        if (empty($colData)) {
            return '';
        }

        $trslSrv = $sm->get('translator');

        //  single RefData (check, it is NOT an array of entities)
        if (isset($colData['description'])) {
            return $trslSrv->translate($colData['description']);
        }

        //  array of RefData
        $result = [];
        foreach ($colData as $row) {
            $result[] = $trslSrv->translate($row['description']);
        }

        $sprtr = (isset($column['separator']) ? $column['separator'] : '');

        return implode($sprtr, $result);
    }
}
