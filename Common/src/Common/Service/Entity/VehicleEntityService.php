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
     * Define entity for default behaviour
     *
     * @var string
     */
    protected $entity = 'Vehicle';

    protected $licencesForVehicleBundle = array(
        'properties' => array(),
        'children' => array(
            'licenceVehicles' => array(
                'properties' => array(),
                'children' => array(
                    'licence' => array(
                        'properties' => array(
                            'id',
                            'licNo'
                        ),
                        'children' => array(
                            'applications' => array(
                                'properties' => array(
                                    'id'
                                )
                            )
                        )
                    )
                )
            )
        )
    );

    public function getLicencesForVrm($vrm)
    {
        $results = $this->get(array('vrm' => $vrm), $this->licencesForVehicleBundle);

        $return = array();

        foreach ($results['Results'] as $result) {

            $return[] = $result['licenceVehicles'];
        }

        return $return;
    }
}
