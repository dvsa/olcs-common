<?php

namespace Common\Service\Table\Formatter;

use Common\Service\Table\Formatter\Date;
use Zend\ServiceManager\ServiceLocatorInterface;

class TransportManagerDateOfBirth extends Date
{
    /**
     * {@inheritdoc}
     */
    public static function format($data, $column = array(), $sm = null)
    {
        $dob = parent::format($data, $column, $sm);

        if (self::shouldShowStatus($column)) {
            return sprintf('<span class="nowrap">%s %s</span>', $dob, self::getStatusHtml($data, $sm));
        }

        return $dob;
    }

    /**
     * Whether the status should be displayed after the date of birth
     *
     * @param array $column
     *
     * @return bool
     */
    protected static function shouldShowStatus(array $column)
    {
        if (!isset($column['internal']) || (!isset($column['lva']))) {
            return false;
        }

        if ($column['lva'] == 'variation' || $column['lva'] == 'application') {
            return true;
        }

        return false;
    }

    /**
     * Get the html for the status
     *
     * @param array                   $data Row Data
     * @param ServiceLocatorInterface $sm   Service Manager
     *
     * @return string HTML
     */
    protected static function getStatusHtml(array $data, ServiceLocatorInterface $sm)
    {
        $viewHelper = $sm->get('ViewHelperManager')->get('transportManagerApplicationStatus');

        $id = (isset($data['status']['id'])) ? $data['status']['id'] : '';
        $description = (isset($data['status']['description'])) ? $data['status']['description'] : '';

        return $viewHelper->render($id, $description);
    }
}
