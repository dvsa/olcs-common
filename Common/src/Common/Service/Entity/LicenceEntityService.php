<?php

/**
 * Licence Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Entity;

use Common\Exception\DataServiceException;
use Common\RefData;

/**
 * Licence Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicenceEntityService extends AbstractLvaEntityService
{
    private $typeShortCodeMap =[
        RefData::LICENCE_TYPE_RESTRICTED             => 'R',
        RefData::LICENCE_TYPE_STANDARD_INTERNATIONAL => 'SI',
        RefData::LICENCE_TYPE_STANDARD_NATIONAL      => 'SN',
        RefData::LICENCE_TYPE_SPECIAL_RESTRICTED     => 'SR',
    ];

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
            'goodsOrPsv',
            'enforcementArea',
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
            ),
            'transportConsultantCd' => array(
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
            )
        )
    );

    protected $vehicleDataBundle = array(
        'children' => array(
            'licenceVehicles' => array(
                'children' => array(
                    'goodsDiscs',
                    'interimApplication',
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

    protected $vehiclesForTransfer = array(
        'children' => array(
            'licenceVehicles' => array(
                'children' => array(
                    'vehicle',
                    'goodsDiscs'
                ),
                'criteria' => array(
                    'removalDate' => 'NULL'
                )
            ),
            'goodsOrPsv'
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
                    'leadTcArea',
                ],
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
            'changeOfEntitys',
            'trafficArea'
            /*
            'cases' =>   [ // DON'T do this, it's horribly slow for some reason!
                'criteria' => [
                    'closedDate' => 'NULL',
                    'deletedDate' => 'NULL',
                ],
            ],
            */
        )
    );

    protected $revocationDataBundle = [
        'children' => [
            'goodsOrPsv',
            'licenceVehicles' => [
                'children' => [
                    'goodsDiscs'
                ]
            ],
            'psvDiscs',
            'tmLicences'
        ]
    ];

    protected $conditionsUndertakingsBundle = [
        'children' => [
            'conditionUndertakings' => [
                'criteria' => [
                    'isDraft' => '0',
                    'isFulfilled' => '0'
                ],
                'children' => [
                    'conditionType',
                    'attachedTo',
                    'operatingCentre' => [
                        'children' => [
                            'address'
                        ]
                    ]
                ]
            ]
        ]
    ];

    protected $enforcementAreaDataBundle = array(
        'children' => array(
            'enforcementArea'
        )
    );

    protected $operatingCentresDataBundle = array(
        'children' => array(
            'trafficArea',
            'enforcementArea',
        ),
    );

    protected $getByLicenceNumberWithOperatingCentresBundle = array(
        'children' => array(
            'operatingCentres' => array(
                'children' => array(
                    'operatingCentre' => array(
                        'children' => array(
                            'address',
                            'conditionUndertakings' => array(
                                'criteria' => array(
                                    'isFulfilled' => 'Y',
                                    'isDraft' => 'Y'
                                )
                            ),
                            'complaints' => array(
                                'criteria' => array(
                                    'status' => RefData::COMPLAIN_STATUS_OPEN
                                )
                            ),
                            'conditionUndertakings' => array(
                                'criteria' => array(
                                    'isFulfilled' => 'Y',
                                    'isDraft' => 'Y'
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


    public function getRevocationDataForLicence($id)
    {
        return $this->get($id, $this->revocationDataBundle);
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

    public function getVehiclesPsvTotal($id)
    {
        $data = $this->getAll($id, $this->vehiclesPsvTotalBundle);

        return count($data['licenceVehicles']);
    }

    public function getTotalAuths($id)
    {
        return $this->get($id);
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
     * @NOTE this has been migrated [UpdateTrafficArea]
     */
    public function setTrafficArea($licenceId, $trafficAreaId = null)
    {
        $this->forceUpdate($licenceId, array('trafficArea' => $trafficAreaId));

        if ($trafficAreaId) {
            $this->generateLicence($licenceId);
        }

        return $this;
    }

    /**
     * Set enforcement area
     *
     * @param int $licenceId
     * @param int $enforcementAreaId
     * @todo maybe remove?
     */
    public function setEnforcementArea($licenceId, $enforcementAreaId)
    {
        $this->forceUpdate($licenceId, array('enforcementArea' => $enforcementAreaId));
        return $this;
    }

    /**
     * Generates new licences or updates existing one and saves it to licence entity
     *
     * @NOTE This functionality has been replicated in the API [Licence/GenerateLicenceNumber]
     *
     * @param string $licenceId
     */
    public function generateLicence($licenceId)
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

            if (!isset($licenceGen['id'])) {
                throw new DataServiceException('Error generating licence');
            }

            $saveData['licNo'] = sprintf(
                '%s%s%s',
                $licenceCat === RefData::LICENCE_CATEGORY_PSV ? 'P' : 'O',
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

    /**
     * @todo maybe remove?
     */
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

    public function getWithOrganisation($licenceId)
    {
        return $this->get($licenceId, $this->organisationBundle);
    }

    public function getOrganisation($licenceId)
    {
        $response = $this->getWithOrganisation($licenceId);

        return $response['organisation'];
    }

    public function getVehiclesPsvDataForApplication($applicationId)
    {
        $bundle = $this->vehiclePsvDataBundle;

        $licenceId = $this->getServiceLocator()->get('Entity\Application')
            ->getLicenceIdForApplication($applicationId);

        // So to grab the relevant licence vehicles...
        $bundle['children']['licenceVehicles']['criteria'] = [
            'removalDate' => 'NULL',
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
            RefData::LICENCE_STATUS_VALID,
            RefData::LICENCE_STATUS_SUSPENDED,
            RefData::LICENCE_STATUS_CURTAILED,
        ];
        $bundle['children']['organisation']['children']['licences']['criteria'] = [
            'status' => 'IN ' . json_encode($licenceStatuses)
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
                        'id' => 'IN ' . json_encode($ids)
                    ]
                ]
            ]
        ];
        return $this->get($licenceId, $bundle)['communityLics'];
    }

    /**
     * Get community licences by licence id
     *
     * @param int $licenceId
     * @return array
     */
    public function getCommunityLicencesByLicenceId($licenceId)
    {
        $bundle = [
            'children' => [
                'communityLics' => [
                    'children' => [
                        'status',
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
        if (array_key_exists($type, $this->typeShortCodeMap)) {
            return $this->typeShortCodeMap[$type];
        }
    }

    /**
     * @param int $id licence id
     * @param string $status
     */
    public function setLicenceStatus($id, $status)
    {
        return $this->forceUpdate($id, ['status' => $status]);
    }

    public function getConditionsAndUndertakings($id)
    {
        return $this->get($id, $this->conditionsUndertakingsBundle);
    }

    /**
     * Get enforcement area
     *
     * @param int $id
     * @return array
     */
    public function getEnforcementArea($id)
    {
        return $this->get($id, $this->enforcementAreaDataBundle);
    }

    /**
     * Get all other active statuses
     *
     * @param int $licenceId
     * @return array
     *
     * @todo maybe remove?
     */
    public function getOtherActiveLicences($licenceId)
    {
        $valid = [
            RefData::LICENCE_STATUS_SUSPENDED,
            RefData::LICENCE_STATUS_VALID,
            RefData::LICENCE_STATUS_CURTAILED
        ];
        $licence = $this->getHeaderParams($licenceId);
        $query = [
            'organisation' => $licence['organisation']['id'],
            'status' => 'IN ["' . implode('","', $valid) . '"]',
            'goodsOrPsv' => $licence['goodsOrPsv']['id']
        ];
        if ($licence['goodsOrPsv']['id'] == RefData::LICENCE_CATEGORY_PSV) {
            $query['licenceType'] = "!= " . RefData::LICENCE_TYPE_SPECIAL_RESTRICTED;
        }

        $results = $this->getAll($query, $this->overviewBundle);
        // we can't filter by id, Zend AbstractRestController will
        // return only one value if we pass id as a parameter
        $filtered = [];
        foreach ($results['Results'] as $result) {
            if ($result['id'] != $licenceId) {
                $filtered[$result['id']] = $result['licNo'];
            }
        }
        return $filtered;
    }

    /**
     * Get licence with vehicles
     *
     * @param int $licenceId
     * @return array
     */
    public function getLicenceWithVehicles($licenceId)
    {
        return $this->getAll($licenceId, $this->vehiclesForTransfer);
    }

    /**
     * Get vehicle ids by licence vehicle ids
     *
     * @param int $sourceLicenceId
     * @param array $ids
     * @return array
     */
    public function getVehiclesIdsByLicenceVehiclesIds($sourceLicenceId, $ids)
    {
        $licence = $this->getLicenceWithVehicles($sourceLicenceId);
        $vehicles = [];
        foreach ($licence['licenceVehicles'] as $lv) {
            if (array_search($lv['id'], $ids) !== false) {
                $vehicles[$lv['vehicle']['vrm']] = $lv['vehicle']['id'];
            }
        }
        return $vehicles;
    }

    /**
     * Get licence by licence number and return all operating centres and complaints.
     *
     * @param $licNo
     *
     * @return array
     */
    public function getByLicenceNumberWithOperatingCentres($licNo)
    {
        return $this->getAll(
            array(
                'licNo' => $licNo
            ),
            $this->getByLicenceNumberWithOperatingCentresBundle
        );
    }
}
