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

        return '<a href="' . $url(
            static::$route,
            array(
                'id' => $data['id'],
                'action' => 'edit'
            ),
            array(),
            true
        ) . '">' . $data['vrm'] . '</a>';
    }
}
