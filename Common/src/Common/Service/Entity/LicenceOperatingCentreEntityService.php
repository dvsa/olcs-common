<?php

/**
 * Licence Operating Centre Entity Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\Service\Entity;

/**
 * Licence Operating Centre Entity Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class LicenceOperatingCentreEntityService extends AbstractOperatingCentreEntityService
{
    protected $entity = 'LicenceOperatingCentre';

    protected $type = 'licence';

    private $ocAuthorisationsBundle = array(
        'properties' => array(
            'noOfVehiclesPossessed',
            'noOfTrailersPossessed'
        )
    );

    public function getVehicleAuths($id)
    {
        return $this->get($id, $this->ocAuthorisationsBundle);
    }
}
