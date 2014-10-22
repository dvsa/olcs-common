<?php

/**
 * Licence Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Entity;

/**
 * Licence Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicenceEntityService extends AbstractEntityService
{
    /**
     * Goods or PSV keys
     */
    const LICENCE_CATEGORY_GOODS_VEHICLE = 'lcat_gv';
    const LICENCE_CATEGORY_PSV = 'lcat_psv';

    /**
     * Licence types keys
     */
    const LICENCE_TYPE_RESTRICTED = 'ltyp_r';
    const LICENCE_TYPE_STANDARD_INTERNATIONAL = 'ltyp_si';
    const LICENCE_TYPE_STANDARD_NATIONAL = 'ltyp_sn';
    const LICENCE_TYPE_SPECIAL_RESTRICTED = 'ltyp_sr';

    const LICENCE_STATUS_NEW = 'lsts_new';
    const LICENCE_STATUS_SUSPENDED = 'lsts_suspended';
    const LICENCE_STATUS_VALID = 'lsts_valid';
    const LICENCE_STATUS_CURTAILED = 'lsts_curtailed';

    /**
     * Define entity for default behaviour
     *
     * @var string
     */
    protected $entity = 'Licence';

    /**
     * Holds the overview bundle
     *
     * @var array
     */
    private $overviewBundle = array(
        'properties' => array(
            'id',
            'grantedDate',
            'expiryDate',
            'licNo'
        ),
        'children' => array(
            'status' => array(
                'properties' => array('id')
            )
        )
    );

    /**
     * Holds the bundle to retrieve type of licence bundle
     *
     * @var array
     */
    private $typeOfLicenceBundle = array(
        'properties' => array(
            'version',
            'niFlag'
        ),
        'children' => array(
            'goodsOrPsv' => array(
                'properties' => array('id')
            ),
            'licenceType' => array(
                'properties' => array('id')
            )
        )
    );

    /**
     * Bundle to check whether the application belongs to the organisation
     *
     * @var array
     */
    private $doesBelongToOrgBundle = array(
        'properties' => array(),
        'children' => array(
            'organisation' => array(
                'properties' => array('id')
            )
        )
    );

    /**
     * Header data bundle
     *
     * @var array
     */
    private $headerDataBundle = array(
        'properties' => array(
            'licNo'
        ),
        'children' => array(
            'organisation' => array(
                'properties' => array(
                    'name'
                )
            ),
            'status' => array(
                'properties' => array(
                    'id'
                )
            )
        )
    );

    protected $addressesDataBundle = array(
        'properties' => array(),
        'children' => array(
            'organisation' => array(
                'properties' => array(),
                'children' => array(
                    'contactDetails' => array(
                        'properties' => array(
                            'id',
                            'version',
                            'fao',
                            'emailAddress'
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
                                    'town',
                                    'postcode'
                                ),
                                'children' => array(
                                    'countryCode' => array(
                                        'properties' => array(
                                            'id'
                                        )
                                    )
                                )
                            ),
                            'contactType' => array(
                                'properties' => array(
                                    'id'
                                )
                            )
                        )
                    ),
                )
            ),
            'contactDetails' => array(
                'properties' => array(
                    'id',
                    'version',
                    'fao',
                    'emailAddress'
                ),
                'children' => array(
                    'phoneContacts' => array(
                        'properties' => array(
                            'id',
                            'version',
                            'phoneNumber'
                        ),
                        'children' => array(
                            'phoneContactType' => array(
                                'properties' => array(
                                    'id'
                                )
                            )
                        )
                    ),
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
                    'contactType' => array(
                        'properties' => array(
                            'id'
                        )
                    )
                )
            )
        )
    );

    // @TODO currently duped with application entity service...
    // traitify or move to abstract?
    /**
     * Operating Centres bundle
     */
    protected $ocBundle = array(
        'properties' => array(
            'id',
            'version',
            'totAuthSmallVehicles',
            'totAuthMediumVehicles',
            'totAuthLargeVehicles',
            'totCommunityLicences',
            'totAuthVehicles',
            'totAuthTrailers',
        ),
        'children' => array(
            'licence' => array(
                'properties' => array(
                    'id'
                ),
                'children' => array(
                    'trafficArea' => array(
                        'properties' => array(
                            'id',
                            'name'
                        )
                    )
                )
            ),
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
                                'properties' => array('id')
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
     * Safety data bundle
     *
     * @var array
     */
    protected $safetyDataBundle = array(
        'properties' => array(
            'id',
            'version',
            'safetyInsVehicles',
            'safetyInsTrailers',
            'safetyInsVaries',
            'tachographInsName',
            'isMaintenanceSuitable',
        ),
        'children' => array(
            'tachographIns' => array(
                'properties' => array('id')
            )
        )
    );

    protected $vehicleDataBundle = array(
        'properties' => null,
        'children' => array(
            'licenceVehicles' => array(
                'properties' => array(
                    'id',
                    'receivedDate',
                    'specifiedDate',
                    'deletedDate',
                    'removalDate'
                ),
                'children' => array(
                    'goodsDiscs' => array(
                        'ceasedDate',
                        'discNo'
                    ),
                    'vehicle' => array(
                        'properties' => array(
                            'vrm',
                            'platedWeight'
                        )
                    )
                )
            )
        )
    );

    protected $currentVrmBundle = array(
        'properties' => array(
            'removalDate'
        ),
        'children' => array(
            'vehicle' => array(
                'properties' => array(
                    'vrm'
                )
            )
        )
    );

    protected $vehiclesTotalBundle = array(
        'properties' => array(),
        'children' => array(
            'licenceVehicles' => array(
                'properties' => array('id')
            )
        )
    );

    /**
     * Get data for overview
     *
     * @param int $id
     * @return array
     */
    public function getOverview($id)
    {
        return $this->get($id, $this->overviewBundle);
    }

    /**
     * Get type of licence data
     *
     * @param int $id
     * @return array
     */
    public function getTypeOfLicenceData($id)
    {
        $data = $this->get($id, $this->typeOfLicenceBundle);

        return array(
            'version' => $data['version'],
            'niFlag' => $data['niFlag'],
            'licenceType' => isset($data['licenceType']['id']) ? $data['licenceType']['id'] : null,
            'goodsOrPsv' => isset($data['goodsOrPsv']['id']) ? $data['goodsOrPsv']['id'] : null
        );
    }

    /**
     * Check whether the licence belongs to the organisation
     *
     * @param int $id
     * @param int $orgId
     * @return boolean
     */
    public function doesBelongToOrganisation($id, $orgId)
    {
        $data = $this->get($id, $this->doesBelongToOrgBundle);

        return (isset($data['organisation']['id']) && $data['organisation']['id'] == $orgId);
    }

    /**
     * Get data for header
     *
     * @param int $id
     * @return array
     */
    public function getHeaderParams($id)
    {
        return $this->get($id, $this->headerDataBundle);
    }

    /**
     * Get addresses data
     *
     * @param int $id
     * @return array
     */
    public function getAddressesData($id)
    {
        return $this->get($id, $this->addressesDataBundle);
    }

    /**
     * Get operating centres data
     *
     * @param int $id
     * @return array
     */
    public function getOperatingCentresData($id)
    {
        return $this->get($id, $this->ocBundle);
    }

    /**
     * Get safety data
     *
     * @param int $id
     * @return array
     */
    public function getSafetyData($id)
    {
        return $this->get($id, $this->safetyDataBundle);
    }

    public function getVehiclesData($id)
    {
        return $this->get($id, $this->vehicleDataBundle);
    }

    public function getCurrentVrms($id)
    {
        $data = $this->getServiceLocator()->get('Helper\Rest')
            ->makeRestCall('LicenceVehicle', 'GET', array('licence' => $id), $this->currentVrmBundle);

        $vrms = array();

        foreach ($data['Results'] as $row) {
            if (!$row['removalDate']) {
                $vrms[] = $row['vehicle']['vrm'];
            }
        }

        return $vrms;
    }

    public function getVehiclesTotal($id)
    {
        $data = $this->get($id, $this->vehiclesTotalBundle);

        return count($data['licenceVehicles']);
    }
}
