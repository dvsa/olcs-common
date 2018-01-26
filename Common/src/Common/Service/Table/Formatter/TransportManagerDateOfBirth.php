<?php


namespace Common\Service\Table\Formatter;

use Common\Service\Table\Formatter\Date;
use Zend\ServiceManager\ServiceLocatorInterface;

class TransportManagerDateOfBirth extends Date
{

    public static function format($data, $column = array(), $sm = null)
    {
        $dob = parent::format($data, $column, $sm);

        if (self::shouldShowStatus($column)) {
            $dob = sprintf($dob . " %s", self::getStatusHtml($data, $sm));
        }

        return $dob;

    }


    protected static function shouldShowStatus($column = array())
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
    protected static function getStatusHtml($data, $sm)
    {
        $viewHelper = $sm->get('ViewHelperManager')->get('transportManagerApplicationStatus');

        $id = (isset($data['status']['id'])) ? $data['status']['id'] : '';
        $description = (isset($data['status']['description'])) ? $data['status']['description'] : '';

        return $viewHelper->render($id, $description);
    }
}
