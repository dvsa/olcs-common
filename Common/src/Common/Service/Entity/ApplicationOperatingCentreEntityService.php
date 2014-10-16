<?php

/**
 * Application Operating Centre Entity Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\Service\Entity;

/**
 * Application Operating Centre Entity Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class ApplicationOperatingCentreEntityService extends AbstractEntityService
{
    protected $entity = 'ApplicationOperatingCentre';
    /**
     * Operating Centres Table data bundle
     *
     * @var array
     */
    protected $addressBundle = array(
        'properties' => array(
            'id',
            'permission',
            'adPlaced',
            'noOfVehiclesPossessed',
            'noOfTrailersPossessed'
        ),
        'children' => array(
            'operatingCentre' => array(
                'properties' => array(
                    'id',
                    'version'
                ),
                'children' => array(
                    'address' => array(
                        'properties' => array(
                            'id',
                            'version',
                            'addressLine1',
                            'addressLine2',
                            'addressLine3',
                            'addressLine4',
                            'postcode',
                            'town'
                        ),
                        'children' => array(
                            'countryCode' => array(
                                'properties' => array(
                                    'id'
                                )
                            )
                        )
                    ),
                    'adDocuments' => array(
                        'properties' => array(
                            'id',
                            'version',
                            'filename',
                            'identifier',
                            'size'
                        )
                    )
                )
            )
        )
    );

    public function getAddressData($applicationId)
    {
        return $this->getServiceLocator()->get('Helper\Rest')
            ->makeRestCall($this->entity, 'GET', array('application' => $applicationId), $this->addressBundle);
    }
}
