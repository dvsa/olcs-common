<?php

namespace Common\Service\Table\Formatter;

use Zend\ServiceManager\ServiceLocatorInterface;
use Common\Service\Table\Formatter\RefData as RefDataFormatter;

/**
 * Fetches the refdata and then adds status formatting
 */
class RefDataStatus implements FormatterInterface
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
        $description = RefDataFormatter::format($data, $column, $sm);

        $status = [
            'id' => $data[$column['name']]['id'],
            'description' => $description
        ];

        /** @var \Common\View\Helper\Status $statusHelper */
        $statusHelper = $sm->get('ViewHelperManager')->get('status');

        return $statusHelper->__invoke($status);
    }
}
