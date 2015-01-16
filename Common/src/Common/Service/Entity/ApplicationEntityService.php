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
    const APPLICATION_STATUS_GRANTED = 'apsts_granted';
    const APPLICATION_STATUS_UNDER_CONSIDERATION = 'apsts_consideration';
    const APPLICATION_STATUS_VALID = 'apsts_valid';

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
        'children' => array(
            'licence' => array(
                'children' => array(
                    'organisation'
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
        'children' => array(
            'applicationCompletions',
            'status'
        )
    );

    /**
     * Holds the bundle to retrieve a licence id for an application
     *
     * @var array
     */
    private $licenceIdForApplicationBundle = array(
        'children' => array(
            'licence'
        )
    );

    /**
     * Bundle to retrieve data to update completion status
     *
     * @var array
     */
    private $completionStatusDataBundle = array(
        'children' => array(
            'goodsOrPsv',
            'licenceType',
            'operatingCentres',
            'previousConvictions',
            'previousLicences' => array(
                'children' => array(
                    'previousLicenceType'
                )
            ),
            'licence' => array(
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
                    ),
                    'privateHireLicences',
                    'organisation' => array(
                        'children' => array(
                            'type',
                            'tradingNames',
                            'organisationPersons',
                            'contactDetails' => array(
                                'children' => array(
                                    'contactType'
                                )
                            )
                        )
                    ),
                    'correspondenceCd' => array(
                        'children' => array(
                            'phoneContacts'
                        )
                    ),
                    'establishmentCd',
                    'tachographIns',
                    'workshops',
                    'trafficArea'
                )
            ),
        )
    );

    /**
     * Bundle to retrieve data for processing page
     *
     * @var array
     */
    private $processingDataBundle = array(
        'children' => array(
            'status',
            'licence' => array(
                'children' => array(
                    'status',
                    'goodsOrPsv',
                    'licenceType',
                    'trafficArea',
                    'organisation'
                )
            )
        )
    );

    /**
     * Cache the mapping of application ids to licence ids
     *
     * @var array
     */
    private $licenceIds = array();

    /**
     * Header data bundle
     *
     * @var array
     */
    private $headerDataBundle = array(
        'children' => array(
            'status',
            'licence' => array(
                'children' => array(
                    'organisation'
                )
            )
        )
    );

    /**
     * Task data bundle
     *
     * @var array
     */
    private $taskDataBundle = array(
        'children' => array(
            'licence'
        )
    );

    /**
     * Safety Data bundle
     *
     * @var array
     */
    protected $safetyDataBundle = array(
        'children' => array(
            'licence' => array(
                'children' => array(
                    'tachographIns'
                )
            )
        )
    );

    protected $statusBundle = array(
        'children' => array(
            'status'
        )
    );

    protected $vehicleDeclarationDataBundle = array(
        'children' => array(
            'licence' => array(
                'children' => array(
                    'trafficArea'
                )
            )
        )
    );

    protected $totalNumberOfVehiclesBundle = array(
        'children' => array(
            'licence' => array(
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
            )
        )
    );

    protected $categoryBundle = array(
        'children' => array(
            'goodsOrPsv'
        )
    );

    protected $validatingDataBundle = array(
        'children' => array(
            'goodsOrPsv',
            'licenceType'
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
     * @param array $applicationData
     */
    public function createNew($organisationId, $applicationData = array())
    {
        $licenceData = array(
            'status' => LicenceEntityService::LICENCE_STATUS_NOT_SUBMITTED,
            'organisation' => $organisationId,
        );

        $licence = $this->getServiceLocator()->get('Entity\Licence')->save($licenceData);

        $applicationData = array_merge(
            $applicationData,
            array(
                'licence' => $licence['id'],
                'status' => self::APPLICATION_STATUS_NOT_SUBMITTED,
                'isVariation' => false
            )
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
     * Create a variation application for a given organisation
     *
     * @param int $licenceId
     * @param array $applicationData
     */
    public function createVariation($licenceId, $applicationData = array())
    {
        $licenceData = $this->getServiceLocator()->get('Entity\Licence')->getVariationData($licenceId);

        $applicationData = array_merge(
            $licenceData,
            array(
                'licence' => $licenceId,
                'status' => self::APPLICATION_STATUS_NOT_SUBMITTED,
                'isVariation' => true
            ),
            // @NOTE The passed in application data has priority, so is last to merge
            $applicationData
        );

        if ($this->getServiceLocator()->has('VariationUtility')) {
            $applicationData = $this->getServiceLocator()->get('VariationUtility')
                ->alterCreateVariationData($applicationData);
        }

        $application = $this->save($applicationData);

        return $application['id'];
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
     * Get data for processing
     * @param int $id
     * @return array
     */
    public function getDataForProcessing($id)
    {
        return $this->get($id, $this->processingDataBundle);
    }

    /**
     * Get data for task stuff
     * @param int $id
     * @return array
     */
    public function getDataForTasks($id)
    {
        return $this->get($id, $this->taskDataBundle);
    }

    /**
     * Get application type
     *
     * @param type $id
     */
    public function getApplicationType($id)
    {
        $data = $this->get($id);

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
        return $this->get($id);
    }

    public function getLicenceHistoryData($id)
    {
        return $this->get($id);
    }

    public function getConvictionsPenaltiesData($id)
    {
        return $this->get($id);
    }

    public function getDataForVehiclesDeclarations($id)
    {
        return $this->get($id, $this->vehicleDeclarationDataBundle);
    }

    public function getStatus($id)
    {
        return $this->get($id, $this->statusBundle)['status']['id'];
    }

    public function getCategory($id)
    {
        return $this->get($id, $this->categoryBundle)['goodsOrPsv']['id'];
    }

    public function getApplicationDate($id)
    {
        $data = $this->get($id);

        if ($data['receivedDate'] === null) {
            return $data['createdOn'];
        }

        return $data['receivedDate'];
    }

    public function getDataForValidating($id)
    {
        $data = $this->get($id, $this->validatingDataBundle);

        $data['licenceType'] = $data['licenceType']['id'];
        $data['goodsOrPsv'] = $data['goodsOrPsv']['id'];

        return array_intersect_key(
            $data,
            [
                'totAuthTrailers' => null,
                'totAuthVehicles' => null,
                'totAuthSmallVehicles' => null,
                'totAuthMediumVehicles' => null,
                'totAuthLargeVehicles' => null,
                'licenceType' => null,
                'goodsOrPsv' => null,
                'niFlag' => null
            ]
        );
    }

    public function getOrganisation($applicationId)
    {
        $licenceId = $this->getLicenceIdForApplication($applicationId);

        return $this->getServiceLocator()->get('Entity\Licence')->getOrganisation($licenceId);
    }

    public function delete($id)
    {
        $licenceId = $this->getLicenceIdForApplication($id);

        $this->getServiceLocator()->get('Entity\Licence')->delete($licenceId);

        parent::delete($id);
    }
}
