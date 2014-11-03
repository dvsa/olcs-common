<?php

/**
 * Application Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Entity;

use Common\Service\Entity\LicenceEntityService;

/**
 * Application Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationEntityService extends AbstractLvaEntityService
{
    const APPLICATION_TYPE_NEW = 0;
    const APPLICATION_TYPE_VARIATION = 1;

    const APPLICATION_STATUS_NOT_SUBMITTED = 'apsts_not_submitted';
    const APPLICATION_STATUS_UNDER_CONSIDERATION = 'apsts_consideration';

    /**
     * Define entity for default behaviour
     *
     * @var string
     */
    protected $entity = 'Application';

    /**
     * Bundle to check whether the application belongs to the organisation
     *
     * @var array
     */
    private $doesBelongToOrgBundle = array(
        'properties' => array(),
        'children' => array(
            'licence' => array(
                'properties' => array(),
                'children' => array(
                    'organisation' => array(
                        'properties' => array('id')
                    )
                )
            )
        )
    );

    /**
     * Holds the overview bundle
     *
     * @var array
     */
    private $overviewBundle = array(
        'properties' => array(
            'id',
            'version',
            'createdOn'
        ),
        'children' => array(
            'applicationCompletions' => array(
                'properties' => 'ALL'
            ),
            'status' => array(
                'properties' => array('id')
            )
        )
    );

    /**
     * Holds the bundle to retrieve a licence id for an application
     *
     * @var array
     */
    private $licenceIdForApplicationBundle = array(
        'properties' => array(),
        'children' => array(
            'licence' => array(
                'properties' => array(
                    'id'
                )
            )
        )
    );

    /**
     * Bundle to retrieve data to update completion status
     *
     * @var array
     */
    private $completionStatusDataBundle = array(
        'properties' => array(
            'safetyConfirmation',
            'bankrupt',
            'liquidation',
            'receivership',
            'administration',
            'disqualified',
            'insolvencyDetails',
            'insolvencyConfirmation',
            'prevHasLicence',
            'prevHadLicence',
            'prevBeenRefused',
            'prevBeenRevoked',
            'prevBeenDisqualifiedTc',
            'prevBeenAtPi',
            'prevPurchasedAssets',
            'prevConviction',
            'convictionsConfirmation',
            'totAuthSmallVehicles',
            'totAuthMediumVehicles',
            'totAuthLargeVehicles',
            'totAuthVehicles',
            'totAuthTrailers',
            'totCommunityLicences',
            'psvOperateSmallVhl',
            'psvSmallVhlNotes',
            'psvSmallVhlConfirmation',
            'psvNoSmallVhlConfirmation',
            'psvLimousines',
            'psvNoLimousineConfirmation',
            'psvOnlyLimousinesConfirmation'
        ),
        'children' => array(
            'operatingCentres' => array(
                'properties' => array(
                    'id'
                )
            ),
            'previousConvictions' => array(
                'properties' => array(
                    'id'
                )
            ),
            'previousLicences' => array(
                'properties' => array(),
                'children' => array(
                    'previousLicenceType' => array(
                        'properties' => array('id')
                    )
                )
            ),
            'licence' => array(
                'properties' => array(
                    'niFlag',
                    'safetyInsVehicles',
                    'safetyInsTrailers',
                    'safetyInsVaries',
                    'tachographInsName'
                ),
                'children' => array(
                    'licenceVehicles' => array(
                        'criteria' => array(
                            'removalDate' => null
                        ),
                        'properties' => array(
                            'id'
                        ),
                        'children' => array(
                            'vehicle' => array(
                                'properties' => array(),
                                'children' => array(
                                    'psvType' => array(
                                        'properties' => array('id')
                                    )
                                )
                            )
                        )
                    ),
                    'privateHireLicences' => array(
                        'properties' => array(
                            'id'
                        )
                    ),
                    'organisation' => array(
                        'properties' => array(
                            'companyOrLlpNo',
                            'name',
                            'type',
                            'tradingNames'
                        ),
                        'children' => array(
                            'type' => array(
                                'properties' => array(
                                    'id'
                                )
                            ),
                            'tradingNames' => array(
                                'properties' => array(
                                    'id',
                                    'name'
                                )
                            ),
                            'organisationPersons' => array(
                                'properties' => array('id')
                            ),
                            'contactDetails' => array(
                                'properties' => array(
                                    'id',
                                    'fao'
                                ),
                                'children' => array(
                                    'contactType' => array(
                                        'properties' => array(
                                            'id'
                                        )
                                    )
                                )
                            )
                        )
                    ),
                    'contactDetails' => array(
                        'properties' => array(
                            'id',
                            'fao'
                        ),
                        'children' => array(
                            'phoneContacts' => array(
                                'properties' => array(
                                    'id',
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
                            'contactType' => array(
                                'properties' => array(
                                    'id'
                                )
                            )
                        )
                    ),
                    'goodsOrPsv' => array(
                        'properties' => array(
                            'id'
                        )
                    ),
                    'licenceType' => array(
                        'properties' => array(
                            'id'
                        )
                    ),
                    'tachographIns' => array(
                        'properties' => array('id')
                    ),
                    'workshops' => array(
                        'properties' => array('id')
                    ),
                    'trafficArea' => array(
                        'properties' => array(
                            'id',
                            'isScottishRules'
                        )
                    )
                )
            ),
        )
    );

    /**
     * Cache the mapping of application ids to licence ids
     *
     * @var array
     */
    private $licenceIds = array();

    /**
     * Application type bundle
     *
     * @var array
     */
    private $applicationTypeBundle = array(
        'properties' => array(
            'isVariation'
        )
    );

    /**
     * Header data bundle
     *
     * @var array
     */
    private $headerDataBundle = array(
        'properties' => array(
            'id'
        ),
        'children' => array(
            'status' => array(
                'properties' => array(
                    'id'
                )
            ),
            'licence' => array(
                'properties' => array(
                    'id',
                    'licNo'
                ),
                'children' => array(
                    'organisation' => array(
                        'properties' => array(
                            'name'
                        )
                    )
                )
            )
        )
    );

    /**
     * Safety Data bundle
     *
     * @var array
     */
    protected $safetyDataBundle = array(
        'properties' => array(
            'version',
            'safetyConfirmation',
            'isMaintenanceSuitable'
        ),
        'children' => array(
            'licence' => array(
                'properties' => array(
                    'version',
                    'safetyInsVehicles',
                    'safetyInsTrailers',
                    'safetyInsVaries',
                    'tachographInsName'
                ),
                'children' => array(
                    'tachographIns' => array(
                        'properties' => array('id')
                    )
                )
            )
        )
    );

    protected $statusBundle = array(
        'properties' => array(),
        'children' => array(
            'status' => array(
                'properties' => array('id')
            )
        )
    );

    protected $financialHistoryBundle = array(
        'properties' => array(
            'id',
            'version',
            'bankrupt',
            'liquidation',
            'receivership',
            'administration',
            'disqualified',
            'insolvencyDetails',
            'insolvencyConfirmation'
        )
    );

    protected $licenceHistoryBundle = array(
        'properties' => array(
            'id',
            'version',
            'prevHasLicence',
            'prevHadLicence',
            'prevBeenRefused',
            'prevBeenRevoked',
            'prevBeenDisqualifiedTc',
            'prevBeenAtPi',
            'prevPurchasedAssets'
        )
    );

    protected $convictionsPenaltiesData = array(
        'properties' => array(
            'version',
            'prevConviction',
            'convictionsConfirmation'
        )
    );

    protected $vehicleDeclarationDataBundle = array(
        'properties' => array(
            'id',
            'version',
            'totAuthSmallVehicles',
            'totAuthMediumVehicles',
            'totAuthLargeVehicles',
            'psvOperateSmallVhl',
            'psvSmallVhlNotes',
            'psvSmallVhlConfirmation',
            'psvNoSmallVhlConfirmation',
            'psvLimousines',
            'psvNoLimousineConfirmation',
            'psvOnlyLimousinesConfirmation'
        ),
        'children' => array(
            'licence' => array(
                'properties' => array(),
                'children' => array(
                    'trafficArea' => array(
                        'properties' => array(
                            'id',
                            'isScottishRules'
                        )
                    )
                )
            )
        )
    );

    /**
     * Vehicle PSV bundle
     *
     * @var array
     */
    protected $vehiclesPsvBundle = array(
        'properties' => array(
            'id',
            'version',
            'totAuthSmallVehicles',
            'totAuthMediumVehicles',
            'totAuthLargeVehicles',
            'hasEnteredReg'
        ),
        'children' => array(
            'licence' => array(
                'properties' => null,
                'children' => array(
                    'licenceVehicles' => array(
                        'properties' => array(
                            'id',
                            'specifiedDate',
                            'deletedDate'
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
            )
        )
    );

    protected $totalNumberOfVehiclesBundle = array(
        'properties' => array(),
        'children' => array(
            'licence' => array(
                'properties' => array(),
                'children' => array(
                    'licenceVehicles' => array(
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
            )
        )
    );

    /**
     * Get applications for a given organisation
     *
     * @param int $organisationId
     */
    public function getForOrganisation($organisationId)
    {
        return $this->getServiceLocator()->get('Entity\Organisation')->getApplications($organisationId);
    }

    /**
     * Create a new application for a given organisation
     *
     * @param int $organisationId
     */
    public function createNew($organisationId)
    {
        $licenceData = array(
            'status' => LicenceEntityService::LICENCE_STATUS_NEW,
            'organisation' => $organisationId,
        );

        $licence = $this->getServiceLocator()->get('Entity\Licence')->save($licenceData);

        $applicationData = array(
            'licence' => $licence['id'],
            'status' => self::APPLICATION_STATUS_NOT_SUBMITTED,
            'isVariation' => false
        );

        $application = $this->save($applicationData);

        $applicationCompletionData = [
            'application' => $application['id'],
        ];

        $this->getServiceLocator()->get('Entity\ApplicationCompletion')->save($applicationCompletionData);

        return array(
            'application' => $application['id'],
            'licence' => $licence['id']
        );
    }

    /**
     * Check whether the application belongs to the organisation
     *
     * @param int $id
     * @param int $orgId
     * @return boolean
     */
    public function doesBelongToOrganisation($id, $orgId)
    {
        $data = $this->get($id, $this->doesBelongToOrgBundle);

        return (isset($data['licence']['organisation']['id']) && $data['licence']['organisation']['id'] == $orgId);
    }

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
     * Get licence for the given application id
     *
     * @param int $id
     * @param array $bundle
     * @return array
     */
    public function getLicenceIdForApplication($id)
    {
        if (!isset($this->licenceIds[$id])) {
            $data = $this->get($id, $this->licenceIdForApplicationBundle);

            $this->licenceIds[$id] = $data['licence']['id'];
        }

        return $this->licenceIds[$id];
    }

    /**
     * Get data for completion status
     * @param int $id
     * @return array
     */
    public function getDataForCompletionStatus($id)
    {
        return $this->get($id, $this->completionStatusDataBundle);
    }

    /**
     * Get application type
     *
     * @param type $id
     */
    public function getApplicationType($id)
    {
        $data = $this->get($id, $this->applicationTypeBundle);

        if (!isset($data['isVariation'])) {
            throw new Exceptions\UnexpectedResponseException('Is variation flag not found');
        }

        if ($data['isVariation']) {
            return self::APPLICATION_TYPE_VARIATION;
        }

        return self::APPLICATION_TYPE_NEW;
    }

    /**
     * Get data for header
     *
     * @param int $id
     * @return array
     */
    public function getHeaderData($id)
    {
        return $this->get($id, $this->headerDataBundle);
    }

    /**
     * Get safety data
     *
     * @param int $id
     */
    public function getSafetyData($id)
    {
        return $this->get($id, $this->safetyDataBundle);
    }

    public function getFinancialHistoryData($id)
    {
        return $this->get($id, $this->financialHistoryBundle);
    }

    public function getLicenceHistoryData($id)
    {
        return $this->get($id, $this->licenceHistoryBundle);
    }

    public function getConvictionsPenaltiesData($id)
    {
        return $this->get($id, $this->convictionsPenaltiesData);
    }

    public function getDataForVehiclesDeclarations($id)
    {
        return $this->get($id, $this->vehicleDeclarationDataBundle);
    }

    public function getStatus($id)
    {
        return $this->get($id, $this->statusBundle)['status']['id'];
    }

    public function getDataForVehiclesPsv($id)
    {
        return $this->get($id, $this->vehiclesPsvBundle);
    }
}
