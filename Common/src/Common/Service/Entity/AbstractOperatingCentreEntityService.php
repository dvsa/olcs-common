<?php

/**
 * Abstract Operating Centre Entity Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\Service\Entity;

/**
 * Abstract Operating Centre Entity Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
abstract class AbstractOperatingCentreEntityService extends AbstractEntityService
{
    protected $type = null;
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
            'noOfVehiclesRequired',
            'noOfTrailersRequired'
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
            'noOfTrailersRequired',
            'noOfVehiclesRequired',
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

    /**
     * OC Count Bundle
     *
     * @var array
     */
    protected $ocCountBundle = array(
        'properties' => array('id')
    );

    public function getAddressSummaryData($lvaId)
    {
        return $this->get(array($this->type => $lvaId), $this->addressSummaryBundle);
    }

    public function getAddressData($id)
    {
        return $this->get($id, $this->addressBundle);
    }

    public function getOperatingCentresCount($lvaId)
    {
        return $this->get(array($this->type => $lvaId), $this->ocCountBundle);
    }
}
