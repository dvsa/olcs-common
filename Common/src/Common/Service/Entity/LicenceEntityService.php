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
     * Northern Ireland Traffic Area Code
     */
    const NORTHERN_IRELAND_TRAFFIC_AREA_CODE = 'N';

    /**
     * Define entity for default behaviour
     *
     * @var string
     */
    protected $entity = 'Licence';

    /**
     * Holds the cached Traffic Area details
     *
     * @var string
     */
    protected $trafficArea;

    /**
     * Cache the TA value options
     *
     * @var array
     */
    private $trafficAreaValueOptions;


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
     * Application traffic area bundle
     *
     * @var array
     */
    protected $trafficAreaBundle = array(
        'properties' => array(),
        'children' => array(
            'trafficArea' => array(
                'properties' => array(
                    'id',
                    'name'
                )
            )
        )
    );

    /**
     * Licence details for traffic area
     *
     * @var array
     */
    protected $licDetailsForTaBundle = array(
        'properties' => array(
            'id',
            'version'
        )
    );

    private $totalAuthorisationsBundle = array(
        'properties' => array(
            'totAuthVehicles',
            'totAuthTrailers'
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

    /**
     * Get traffic area for licence
     *
     * @param int $licenceId
     * @return string
     */
    public function getTrafficArea($licenceId)
    {
        if ($this->trafficArea === null) {
            $licence = $this->getServiceLocator()->get('Helper\Rest')
                ->makeRestCall('Licence', 'GET', $licenceId, $this->trafficAreaBundle);

            if (isset($licence['trafficArea'])) {
                $this->trafficArea = $licence['trafficArea'];
            }
        }

        return $this->trafficArea;
    }

    /**
     * Set traffic area to application's licence based on area id
     *
     * @param int $identifier
     * @param int $id
     */
    public function setTrafficArea($identifier, $id = null)
    {
        $licenceDetails = $this->getLicenceDetailsToUpdateTrafficArea($identifier);

        if (isset($licenceDetails['version'])) {

            $data = array(
                'id' => $licenceDetails['id'],
                'version' => $licenceDetails['version'],
                'trafficArea' => $id
            );

            $this->getServiceLocator()->get('Helper\Rest')->makeRestCall('Licence', 'PUT', $data);

            if ($id) {
                $licenceService = $this->getServiceLocator()->get('licence');
                $licenceService->generateLicence($identifier);
            }
        }
    }

    /**
     * Get Traffic Area value options for select element
     *
     * @return array
     */
    public function getTrafficAreaValueOptions()
    {
        if ($this->trafficAreaValueOptions === null) {
            $trafficArea = $this->get(array(), $this->trafficAreaValuesBundle);

            $this->trafficAreaValueOptions = array();
            $results = $trafficArea['Results'];

            if (!empty($results)) {
                usort(
                    $results,
                    function ($a, $b) {
                        return strcmp($a['name'], $b['name']);
                    }
                );

                foreach ($results as $key => $value) {
                    // Skip Northern Ireland Traffic Area
                    if ($value['id'] == static::NORTHERN_IRELAND_TRAFFIC_AREA_CODE) {
                        continue;
                    }

                    $this->trafficAreaValueOptions[$value['id']] = $value['name'];
                }
            }
        }

        return $this->trafficAreaValueOptions;
    }

    /**
     * Get licence details to update traffic area
     *
     * @return array
     */
    protected function getLicenceDetailsToUpdateTrafficArea($identifier)
    {
        return $this->getServiceLocator()->get('Helper\Rest')
            ->makeRestCall('Licence', 'GET', $identifier, $this->licDetailsForTaBundle);
    }

    public function getTotalAuths($id)
    {
        return $this->get($id, $this->totalAuthorisationsBundle);
    }
}
