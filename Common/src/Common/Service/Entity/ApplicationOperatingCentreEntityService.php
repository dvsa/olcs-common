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
     * Address summary data
     *
     * @var array
     */
    protected $addressSummaryBundle = array(
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

    /**
     * Address data
     *
     * @var array
     */
    protected $addressBundle = array(
        'properties' => array(
            'id',
            'version',
            'noOfTrailersPossessed',
            'noOfVehiclesPossessed',
            'sufficientParking',
            'permission',
            'adPlaced',
            'adPlacedIn',
            'adPlacedDate'
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

    public function getAddressSummaryDataForApplication($applicationId)
    {
        return $this->getServiceLocator()->get('Helper\Rest')
            ->makeRestCall($this->entity, 'GET', array('application' => $applicationId), $this->addressSummaryBundle);
    }

    public function getAddressData($id)
    {
        return $this->getServiceLocator()->get('Helper\Rest')
            ->makeRestCall($this->entity, 'GET', $id, $this->addressBundle);
    }
}
