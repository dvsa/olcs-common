<?php

namespace Common\Service\Table\Formatter;

use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Data Retention Assigned To
 */
class DataRetentionAssignedTo implements FormatterInterface
{
    /**
     * Format column value
     *
     * @param array                   $data   Row data
     * @param array                   $column Column Parameters
     * @param ServiceLocatorInterface $sm     Service Manager
     *
     * @return string
     */
    public static function format($data, array $column = [], ServiceLocatorInterface $sm = null)
    {
        if (isset($data['assignedTo']['contactDetails']['person'])) {
            /**
             * @var \Laminas\View\HelperPluginManager $viewHelperManager
             * @var \Common\View\Helper\PersonName $personName
             */
            $viewHelperManager = $sm->get('ViewHelperManager');
            $personName = $viewHelperManager->get('personName');

            return $personName->__invoke(
                $data['assignedTo']['contactDetails']['person'],
                [
                    'forename',
                    'familyName'
                ]
            );
        }

        return '';
    }
}
