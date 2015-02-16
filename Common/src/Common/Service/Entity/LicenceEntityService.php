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

    const LICENCE_STATUS_UNDER_CONSIDERATION = 'lsts_consideration';
    const LICENCE_STATUS_NOT_SUBMITTED = 'lsts_not_submitted';
    const LICENCE_STATUS_SUSPENDED = 'lsts_suspended';
    const LICENCE_STATUS_VALID = 'lsts_valid';
    const LICENCE_STATUS_CURTAILED = 'lsts_curtailed';
    const LICENCE_STATUS_GRANTED = 'lsts_granted';
    const LICENCE_STATUS_SURRENDERED = 'lsts_surrendered';

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
        'children' => array(
            'licenceType',
            'status',
            'goodsOrPsv'
        )
    );

    /**
     * Bundle to check whether the application belongs to the organisation
     *
     * @var array
     */
    private $doesBelongToOrgBundle = array(
        'children' => array(
            'organisation'
        )
    );

    /**
     * Header data bundle
     *
     * @var array
     */
    private $headerDataBundle = array(
        'children' => array(
            'organisation',
            'status',
            'goodsOrPsv'
        )
    );

    protected $addressesDataBundle = array(
        'children' => array(
            'correspondenceCd' => array(
                'children' => array(
                    'address' => array(
                        'children' => array(
                            'countryCode'
                        )
                    ),
                    'phoneContacts' => array(
                        'children' => array(
                            'phoneContactType'
                        )
                    )
                )
            ),
            'establishmentCd' => array(
                'children' => array(
                    'address' => array(
                        'children' => array(
                            'countryCode'
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
        'children' => array(
            'tachographIns'
        )
    );

    protected $vehicleDataBundle = array(
        'children' => array(
            'licenceVehicles' => array(
                'children' => array(
                    'goodsDiscs',
                    'vehicle'
                )
            )
        )
    );

    protected $vehiclePsvDataBundle = array(
        'children' => array(
            'licenceVehicles' => array(
                'children' => array(
                    'vehicle' => array(
                        'children' => array(
                            'psvType'
                        )
                    )
                )
            )
        )
    );

    protected $vehiclesTotalBundle = array(
        'children' => array(
            'licenceVehicles' => array(
                'criteria' => array(
                    'removalDate' => 'NULL'
                )
            )
        )
    );

    /**
     * Application traffic area bundle
     *
     * @var array
     */
    protected $trafficAreaBundle = array(
        'children' => array(
            'trafficArea'
        )
    );

    protected $licenceNoGenBundle = array(
        'children' => array(
            'trafficArea',
            'applications' => array(
                'criteria' => array(
                    'isVariation' => false
                ),
                'children' => array(
                    'goodsOrPsv'
                )
            )
        )
    );

    protected $psvDiscsBundle = array(
        'children' => array(
            'psvDiscs'
        )
    );

    protected $vehiclesPsvTotalBundle = array(
        'children' => array(
            'licenceVehicles' => array(
                'criteria' => array(
                    'removalDate' => 'NULL'
                ),
                'children' => array(
                    'vehicle' => array(
                        'children' => array(
                            'psvType'
                        )
                    )
                )
            )
        )
    );

    protected $categoryBundle = array(
        'children' => array(
            'goodsOrPsv'
        )
    );

    protected $organisationBundle = array(
        'children' => array(
            'organisation'
        )
    );

    protected $extendedOverviewBundle = array(
        'children' => array(
            'licenceType',
            'status',
            'goodsOrPsv',
            'organisation' => [
                'children' => [
                    'tradingNames',
                    'licences' => [
                        'children' => ['status'],
                    ],
                    'leadTcArea'
                ],
            ],
            'applications' => [
                'children' => ['status'],
            ],
            'psvDiscs' => [
                'criteria' => [
                    'ceasedDate' => 'NULL',
                ],
            ],
            'licenceVehicles' => [
                'criteria' => [
                    'specifiedDate' => 'NOT NULL',
                    'removalDate' => 'NULL',
                ],
            ],
            'operatingCentres',
            /*
            'cases' =>   [ // DON'T do this, it's horribly slow for some reason!
                'criteria' => [
                    'closeDate' => 'NULL',
                    'deletedDate' => 'NULL',
                ],
            ],
            */
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
        $data = $this->getAll($id, $this->vehiclesTotalBundle);

        return count($data['licenceVehicles']);
    }

    public function getVehiclesPsvTotal($id, $type)
    {
        $data = $this->getAll($id, $this->vehiclesPsvTotalBundle);

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
        return $this->get($id);
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
        $this->forceUpdate($licenceId, array('trafficArea' => $trafficAreaId));

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

        if (!isset($licence['applications'][0]['goodsOrPsv']['id']) || !isset($licence['trafficArea']['id'])) {
            return;
        }

        $licenceCat = $licence['applications'][0]['goodsOrPsv']['id'];

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
                $licenceCat === self::LICENCE_CATEGORY_PSV ? 'P' : 'O',
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
    }

    public function findByIdentifier($identifier)
    {
        $result = $this->get(['licNo' => $identifier]);
        if ($result['Count'] === 0) {
            return false;
        }
        return $result['Results'][0];
    }

    public function findByIdentifierWithOrganisation($identifier)
    {
        $result = $this->get(['licNo' => $identifier], $this->organisationBundle);
        if ($result['Count'] === 0) {
            return false;
        }
        return $result['Results'][0];
    }

    public function getVariationData($id)
    {
        $data = $this->get($id, $this->typeOfLicenceBundle);

        $keys = [
            'totAuthTrailers',
            'totAuthVehicles',
            'totAuthSmallVehicles',
            'totAuthMediumVehicles',
            'totAuthLargeVehicles',
            'niFlag'
        ];

        $variationData = array_intersect_key($data, array_flip($keys));

        $variationData['licenceType'] = $data['licenceType']['id'];
        $variationData['goodsOrPsv'] = $data['goodsOrPsv']['id'];

        return $variationData;
    }

    public function getOrganisation($licenceId)
    {
        $response = $this->get($licenceId, $this->organisationBundle);

        return $response['organisation'];
    }

    public function getVehiclesDataForApplication($applicationId)
    {
        return $this->getGenericVehicleDataForApplication($applicationId, $this->vehicleDataBundle);
    }

    public function getVehiclesPsvDataForApplication($applicationId)
    {
        return $this->getGenericVehicleDataForApplication($applicationId, $this->vehiclePsvDataBundle);
    }

    protected function getGenericVehicleDataForApplication($applicationId, $bundle)
    {
        $licenceId = $this->getServiceLocator()->get('Entity\Application')
            ->getLicenceIdForApplication($applicationId);

        // So to grab the relevant licence vehicles...
        $bundle['children']['licenceVehicles']['criteria'] = [
            [
                // ...either the application id needs to match
                'application' => $applicationId,
                // ...or the vehicles must be specified (i.e. on the licence)
                'specifiedDate' => 'NOT NULL'
            ]
        ];

        $results = $this->getAll($licenceId, $bundle);

        $licenceVehicles = $results['licenceVehicles'];
        $return = [];

        foreach ($licenceVehicles as $vehicle) {
            if (empty($vehicle['specifiedDate'])) {
                array_unshift($return, $vehicle);
            } else {
                array_push($return, $vehicle);
            }
        }

        return $return;
    }

    /**
     * Update community licences count
     *
     * @param int $licenceId
     */
    public function updateCommunityLicencesCount($licenceId)
    {
        $communityLicService = $this->getServiceLocator()->get('Entity\CommunityLic');
        $validLicencesCount = $communityLicService->getValidLicences($licenceId)['Count'];

        $licence = $this->getById($licenceId);
        $data = [
            'id' => $licenceId,
            'version' => $licence['version'],
            'totCommunityLicences' => $validLicencesCount
        ];
        $this->save($data);
    }

    public function getInForceForOrganisation($orgId)
    {
        return $this->get(
            [
                'organisation' => $orgId,
                'inForceDate' => 'NOT NULL'
            ]
        );
    }

    /**
     * Get data for internal overview
     *
     * @param int $id
     * @return array
     */
    public function getExtendedOverview($id)
    {
        $bundle = $this->extendedOverviewBundle;

        // modify bundle to filter other licence statuses
        $licenceStatuses = [
            LicenceEntityService::LICENCE_STATUS_VALID,
            LicenceEntityService::LICENCE_STATUS_SUSPENDED,
            LicenceEntityService::LICENCE_STATUS_CURTAILED,
        ];
        $bundle['children']['organisation']['children']['licences']['criteria'] = [
            'status' => 'IN ["'.implode('","', $licenceStatuses).'"]'
        ];

        $applicationStatuses = [
            ApplicationEntityService::APPLICATION_STATUS_UNDER_CONSIDERATION,
            ApplicationEntityService::APPLICATION_STATUS_GRANTED,
        ];
        $bundle['children']['applications']['criteria'] = [
            'status' => 'IN ["'.implode('","', $applicationStatuses).'"]'
        ];

        return $this->get($id, $bundle);
    }

    /**
     * Get community licences by licence id and ids
     *
     * @param int $licenceId
     * @param array $ids
     * @return array
     */
    public function getCommunityLicencesByLicenceIdAndIds($licenceId, $ids)
    {
        $bundle = [
            'children' => [
                'communityLics' => [
                    'criteria' => [
                        'id' => 'IN [' . implode(',', $ids) . ']'
                    ]
                ]
            ]
        ];
        return $this->get($licenceId, $bundle)['communityLics'];
    }

    /**
     * @param string $type e.g. 'ltyp_sr'
     * @return string e.g. 'SR'
     */
    public function getShortCodeForType($type)
    {
        $map = [
            self::LICENCE_TYPE_RESTRICTED             => 'R',
            self::LICENCE_TYPE_STANDARD_INTERNATIONAL => 'SI',
            self::LICENCE_TYPE_STANDARD_NATIONAL      => 'SN',
            self::LICENCE_TYPE_SPECIAL_RESTRICTED     => 'SR',
        ];

        if (array_key_exists($type, $map)) {
            return $map[$type];
        }
    }

}
