<?php

/**
 * Vehicle Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Entity;

/**
 * Vehicle Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VehicleEntityService extends AbstractEntityService
{
    /**
     * PSV types
     */
    const PSV_TYPE_SMALL  = 'vhl_t_a';
    const PSV_TYPE_MEDIUM = 'vhl_t_b';
    const PSV_TYPE_LARGE  = 'vhl_t_c';

    protected $typeMap = [
        'small'  => self::PSV_TYPE_SMALL,
        'medium' => self::PSV_TYPE_MEDIUM,
        'large'  => self::PSV_TYPE_LARGE
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

    public function getTypeMap() {
        return $this->typeMap;
    }

    public function getPsvTypeFromType($type)
    {
        return isset($this->typeMap[$type]) ? $this->typeMap[$type] : null;
    }

    public function getTypeFromPsvType($psvType)
    {
        $map = array_flip($this->typeMap);
        return isset($map[$psvType]) ? $map[$psvType] : null;
    }

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
