<?php

namespace Common\Service\Table\Formatter;

use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Vehicle Registration Mark Formatter which displays Vehicle Registration mark with
 * an indicator if the licence is an interim licence
 *
 * @author  Richard Ward <richard.ward@bjss.com>
 */
class VehicleRegistrationMark implements FormatterInterface
{
    /**
     * Format a Vehicle Registration Mark with an 'interim' indicator where relevant.
     *
     * @param array                   $data   The row data.
     * @param array                   $column The column data.
     * @param ServiceLocatorInterface $sm     The service manager.
     *
     * @return string The formatted Vehicle Registration Mark
     */
    public static function format($data, $column = array(), ServiceLocatorInterface $sm = null)
    {
        $vrm = $data['vehicle']['vrm'];
        return is_null($data['interimApplication'])
            ? $vrm
            : self::formatInterimValue($sm, $vrm);
    }

    /**
     * Format a Vehicle Registration Mark when an 'interim' indicator is required
     *
     * @param ServiceLocatorInterface $sm  The Service Manager
     * @param string                  $vrm The vehicle registration mark
     *
     * @return string The formatted Vehicle Registration Mark
     */
    private static function formatInterimValue(ServiceLocatorInterface $sm, $vrm)
    {
        return sprintf(
            '%s (%s)',
            $vrm,
            $sm->get('translator')->translate(
                'application_vehicle-safety_vehicle.table.vrm.interim-marker'
            )
        );
    }
}
