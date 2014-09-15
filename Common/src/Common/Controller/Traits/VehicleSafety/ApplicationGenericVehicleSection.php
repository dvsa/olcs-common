<?php

/**
 * Application Generic Vehicle Section Trait
 *
 * Internal/External - Application - Vehicle/VehiclePsv Section
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Traits\VehicleSafety;

/**
 * Application Generic Vehicle Section Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
trait ApplicationGenericVehicleSection
{
    /**
     * Holds the section type
     *
     * @var string
     */
    protected $sectionType = 'Application';

    /**
     * This is extended in the licence section
     *
     * @param array $licenceVehicle
     * @return boolean
     */
    protected function showVehicle($licenceVehicle)
    {
        return true;
    }

    /**
     * Alter data for application
     *
     * @param array $data
     * @return array
     */
    protected function alterDataForApplication($data)
    {
        $data['licence-vehicle']['application'] = $this->getIdentifier();

        return $data;
    }
}
