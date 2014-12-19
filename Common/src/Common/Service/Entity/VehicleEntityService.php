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

    /**
     * Define entity for default behaviour
     *
     * @var string
     */
    protected $entity = 'Vehicle';

    protected $licencesForVehicleBundle = array(
        'children' => array(
            'licenceVehicles' => array(
                'required' => true,
                'criteria' => array(
                    'removalDate' => 'NULL'
                ),
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

    public function getLicencesForVrm($vrm)
    {
        $results = $this->get(array('vrm' => $vrm), $this->licencesForVehicleBundle);

        $return = array();

        foreach ($results['Results'] as $result) {

            $return = array_merge($return, $result['licenceVehicles']);
        }

        foreach ($return as $key => $licenceVehicle) {
            if (isset($licenceVehicle['removalDate']) && empty($licenceVehicle['removalDate'])) {
                unset($return[$key]);
            }
        }

        return $return;
    }
}
