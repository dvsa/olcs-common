<?php

/**
 * Vrm
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Section\VehicleSafety\Vehicle\Formatter;

use Common\Service\Table\Formatter\FormatterInterface;

/**
 * Vrm
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Vrm implements FormatterInterface
{
    protected static $route = 'Application/VehicleSafety/Vehicle';

    /**
     * Format an cell
     *
     * @param array $data
     * @param array $column
     * @param \Zend\ServiceManager\ServiceManager $sm
     */
    public static function format($data, $column = array(), $sm = null)
    {
        $url = $sm->get('viewhelpermanager')->get('url');

        $action = 'edit';

        if (isset($column['action-type'])) {
            $action = $column['action-type'] . '-' . $action;
        }

        return '<a href="' . $url(
            static::getRouteForColumn($column),
            array(
                'id' => $data['id'],
                'action' => $action
            ),
            array(),
            true
        ) . '">' . $data['vrm'] . '</a>';
    }

    /**
     * Return the route for the column
     *
     * @param array $column
     * @return string
     */
    protected static function getRouteForColumn($column)
    {
        return static::$route . (isset($column['psv']) && $column['psv'] ? 'Psv' : '');
    }
}
