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
class LicenceEntityService extends AbstractLvaEntityService
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
                            ),
                            'phoneContacts' => array(
                                'properties' => array(
                                    'phoneNumber',
                                    'version',
                                    'id'
                                ),
                                'children' => array(
                                    'phoneContactType' => array(
                                        'properties' => array('id')
                                    )
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

    protected $vehiclePsvDataBundle = array(
        'properties' => null,
        'children' => array(
            'licenceVehicles' => array(
                'properties' => array(
                    'id',
                    'specifiedDate',
                    'deletedDate',
                    'removalDate'
                ),
                'children' => array(
                    'vehicle' => array(
                        'properties' => array(
                            'vrm',
                            'isNovelty',
                        ),
                        'children' => array(
                            'psvType' => array(
                                'properties' => array('id')
                            )
                        )
                    )
                )
            )
        )
    );

    protected $vehiclesTotalBundle = array(
        'properties' => array(),
        'children' => array(
            'licenceVehicles' => array(
                'criteria' => array(
                    'removalDate' => null
                ),
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

    protected $licenceNoGenBundle = array(
        'properties' => array(
            'id',
            'licNo',
            'version'
        ),
        'children' => array(
            'trafficArea' => array(
                'properties' => array(
                    'id',
                )
            ),
            'goodsOrPsv' => array(
                'properties' => array(
                    'id'
                )
            )
        )
    );

    private $totalAuthorisationsBundle = array(
        'properties' => array(
            'totAuthVehicles',
            'totAuthTrailers'
        )
    );

    protected $psvDiscsBundle = array(
        'properties' => array(
            'id',
            'totAuthSmallVehicles',
            'totAuthMediumVehicles',
            'totAuthLargeVehicles'
        ),
        'children' => array(
            'psvDiscs' => array(
                'properties' => array(
                    'id',
                    'discNo',
                    'issuedDate',
                    'ceasedDate',
                    'isCopy'
                )
            )
        )
    );

    protected $vehiclesPsvTotalBundle = array(
        'properties' => array(),
        'children' => array(
            'licenceVehicles' => array(
                'criteria' => array(
                    'removalDate' => null
                ),
                'properties' => array(),
                'children' => array(
                    'vehicle' => array(
                        'properties' => array(
                            'id'
                        ),
                        'children' => array(
                            'psvType' => array(
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
        return $this->get($id, $this->vehicleDataBundle)['licenceVehicles'];
    }

    public function getVehiclesPsvData($id)
    {
        return $this->get($id, $this->vehiclePsvDataBundle)['licenceVehicles'];
    }

    public function getCurrentVrms($id)
    {
        return $this->getServiceLocator()
            ->get('Entity\LicenceVehicle')
            ->getCurrentVrmsForLicence($id);
    }

    public function getVehiclesTotal($id)
    {
        $data = $this->get($id, $this->vehiclesTotalBundle);

        return count($data['licenceVehicles']);
    }

    public function getVehiclesPsvTotal($id, $type)
    {
        $data = $this->get($id, $this->vehiclesPsvTotalBundle);

        $count = 0;

        foreach ($data['licenceVehicles'] as $vehicle) {
            if (isset($vehicle['vehicle']['psvType']['id']) && $vehicle['vehicle']['psvType']['id'] === $type) {
                $count++;
            }
        }

        return $count;
    }

    public function getTotalAuths($id)
    {
        return $this->get($id, $this->totalAuthorisationsBundle);
    }

    public function getPsvDiscsRequestData($id)
    {
        return $this->get($id, $this->psvDiscsBundle);
    }

    public function getPsvDiscs($id)
    {
        return $this->get($id, $this->psvDiscsBundle)['psvDiscs'];
    }

    /**
     * Get traffic area for licence
     *
     * @param int $id
     * @return string
     */
    public function getTrafficArea($id)
    {
        $licence = $this->get($id, $this->trafficAreaBundle);

        if (isset($licence['trafficArea'])) {
            return $licence['trafficArea'];
        }

        return null;
    }

    /**
     * Set traffic area to application's licence based on area id
     *
     * @param int $licenceId
     * @param int $trafficAreaId
     */
    public function setTrafficArea($licenceId, $trafficAreaId = null)
    {
        $this->forcePut($licenceId, array('trafficArea' => $trafficAreaId));

        if ($trafficAreaId) {
            $this->generateLicence($licenceId);
        }
    }

    /**
     * Generates new licences or updates existing one and saves it to licence entity
     *
     * @param string $licenceId
     * @return string|bool
     */
    protected function generateLicence($licenceId)
    {
        $licence = $this->get($licenceId, $this->licenceNoGenBundle);

        if (!isset($licence['goodsOrPsv']['id']) || !isset($licence['trafficArea']['id'])) {
            return;
        }

        $saveData = array(
            'id' => $licence['id'],
            'version' => $licence['version']
        );

        if (empty($licence['licNo'])) {

            $licenceGen = $this->getServiceLocator()->get('Entity\LicenceNoGen')->save(array('licence' => $licenceId));

            if (!isset($licenceGen['id']) ) {
                throw new Exceptions\UnexpectedResponseException('Error generating licence');
            }

            $saveData['licNo'] = sprintf(
                '%s%s%s',
                $licence['goodsOrPsv']['id'] === self::LICENCE_CATEGORY_PSV ? 'P' : 'O',
                $licence['trafficArea']['id'],
                $licenceGen['id']
            );

            $this->save($saveData);

            return;
        }

        if (substr($licence['licNo'], 1, 1) != $licence['trafficArea']['id']) {
            $saveData['licNo'] = sprintf(
                '%s%s%s',
                substr($licence['licNo'], 0, 1),
                $licence['trafficArea']['id'],
                substr($licence['licNo'], 2)
            );

            $this->save($saveData);
        }

        return;
    }
}
