<?php

/**
 * Vehicle Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Entity;

use Common\RefData;

/**
 * Vehicle Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VehicleEntityService extends AbstractEntityService
{
    protected $typeMap = [
        'small'  => RefData::PSV_TYPE_SMALL,
        'medium' => RefData::PSV_TYPE_MEDIUM,
        'large'  => RefData::PSV_TYPE_LARGE
    ];

    /**
     * Define entity for default behaviour
     *
     * @var string
     */
    protected $entity = 'Vehicle';

    protected $licencesForVehicleBundle = array(
        'children' => array(
            'licenceVehicles' => array(
                'children' => array(
                    'licence' => array(
                        'children' => array(
                            'applications'
                        )
                    )
                )
            )
        )
    );

    /*
     * NB Vehicle type has been removed, therefore these methods will not work anymore
     */

    public function getTypeMap()
    {
        return $this->typeMap;
    }

    public function getPsvTypeFromType($type)
    {
        return isset($this->getTypeMap()[$type]) ? $this->getTypeMap()[$type] : null;
    }

    public function getTypeFromPsvType($psvType)
    {
        $map = array_flip($this->typeMap);
        return isset($map[$psvType]) ? $map[$psvType] : null;
    }

    /**
     * @NOTE this has been migrated
     */
    public function getLicencesForVrm($vrm)
    {
        $results = $this->get(array('vrm' => $vrm), $this->licencesForVehicleBundle);

        $return = array();

        foreach ($results['Results'] as $result) {
            foreach ($result['licenceVehicles'] as $licenceVehicle) {
                if (!empty($licenceVehicle['removalDate'])) {
                    continue;
                }
                $return[] = $licenceVehicle;
            }
        }

        return $return;
    }
}
