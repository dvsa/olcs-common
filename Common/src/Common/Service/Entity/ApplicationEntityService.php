<?php

/**
 * Application Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Entity;

use Common\Exception\DataServiceException;
use Common\RefData;

/**
 * Application Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationEntityService extends AbstractLvaEntityService
{
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
            'applicationCompletion',
            'status',
            'interimStatus',
            'licenceType',
            'goodsOrPsv',
            'licence' => array(
                'children' => array(
                    'organisation' => array(
                        'children' => array(
                            'leadTcArea',
                        ),
                    ),
                ),
            ),
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
    private $variationCompletionStatusDataBundle = array(
        'children' => array(
            'licenceType',
            'goodsOrPsv',
            'operatingCentres',
            'transportManagers',
            'licenceVehicles',
            'conditionUndertakings',
            'licence' => array(
                'children' => array(
                    'licenceType',
                    'operatingCentres',
                    'licenceVehicles' => array(
                        'criteria' => array(
                            array(
                                'specifiedDate' => 'NOT NULL'
                            )
                        )
                    ),
                    'psvDiscs' => array(
                        'criteria' => array(
                            'ceasedDate' => 'NULL'
                        )
                    )
                )
            )
        )
    );


    /**
     * Bundle to retrieve data for the interim variation processing.
     *
     * @var array
     */
    private $variationInterimDataBundle = array(
        'children' => array(
            'licenceType',
            'goodsOrPsv',
            'operatingCentres' => array(
                'children' => array(
                    'operatingCentre'
                )
            ),
            'licence' => array(
                'children' => array(
                    'licenceType',
                    'operatingCentres' => array(
                        'children' => array(
                            'operatingCentre'
                        )
                    )
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
        'children' => array(
            'goodsOrPsv',
            'licenceType',
            'operatingCentres',
            'previousConvictions' => array(
                'children' => array('title')
            ),
            'otherLicences' => array(
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
            'transportManagers',
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

    protected $statusBundle = array(
        'children' => array(
            'status',
            'interimStatus',
        )
    );

    protected $vehicleDeclarationDataBundle = array(
        'children' => array(
            'licence' => array(
                'children' => array(
                    'trafficArea'
                )
            ),
            'licenceType'
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

    protected $ocDataForVariationBundle = array(
        'children' => array(
            'licence'
        )
    );

    protected $undertakingsDataBundle = array(
        'children' => [
            'goodsOrPsv',
            'licenceType',
            'licence' => [
                'children' => [
                    'licenceType',
                ],
            ],
        ]
    );

    protected $isUpgradeBundle = array(
        'children' => [
            'licenceType',
            'licence' => [
                'children' => [
                    'licenceType',
                ],
            ],
        ]
    );

    protected $paymentSubmissionBundle = array(
        'children' => [
            'goodsOrPsv',
        ]
    );

    protected $financialEvidenceBundle = array(
        'children' => [
            'licenceType',
            'licence' => [
                'children' => [
                    'organisation'
                ],
                'goodsOrPsv',
            ],
            'goodsOrPsv',
        ]
    );

    /**
     * Bundle to check licence type
     *
     * @var array
     */
    private $licenceTypeBundle = array(
        'children' => array(
            'licenceType'
        )
    );

    /**
     * Interim bundle
     *
     * @var array
     */
    private $interimBundle = array(
        'children' => array(
            'operatingCentres' => array(
                'children' => array(
                    'operatingCentre' => array(
                        'children' => array(
                            'address'
                        )
                    )
                )
            ),
            'licenceVehicles' => array(
                'children' => array(
                    'vehicle',
                    'interimApplication',
                    'goodsDiscs'
                ),
                'criteria' => array(
                    array(
                        'removalDate' => 'NULL'
                    )
                )
            ),
            'interimStatus',
            'licence' => array(
                'children' => array(
                    'communityLics' => array(
                        'children' => array(
                            'status'
                        )
                    )
                )
            )
        )
    );

    protected $tmHeaderBundle = array(
        'children' => [
            'goodsOrPsv',
            'licence'
        ]
    );

    /**
     * @todo migrate me
     */
    protected $operatingCentresDataBundle = array(
        'children' => array(
            'licence' => array(
                'children' => array(
                    'trafficArea',
                    'enforcementArea',
                ),
            ),
        ),
    );

    protected $interimData = null;

    public function getVariationCompletionStatusData($id)
    {
        $bundle = $this->variationCompletionStatusDataBundle;

        $bundle['children']['licence']['children']['licenceVehicles']['criteria'][0]['application'] = $id;

        return $this->get($id, $bundle);
    }

    /**
     * Get the application data for interim processing.
     *
     * @param $id Application ID
     *
     * @return array
     */
    public function getVariationInterimData($id)
    {
        return $this->get($id, $this->variationInterimDataBundle);
    }

    /**
     * Create a variation application for a given organisation
     *
     * @param int $licenceId
     * @param array $applicationData
     * @todo maybe remove?
     */
    public function createVariation($licenceId, $applicationData = array())
    {
        $licenceData = $this->getServiceLocator()->get('Entity\Licence')->getVariationData($licenceId);

        $applicationData = array_merge(
            $licenceData,
            array(
                'licence' => $licenceId,
                'status' => RefData::APPLICATION_STATUS_NOT_SUBMITTED,
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

        // create blank records for Completions and Tracking
        $applicationStatusData = ['application' => $application['id']];

        $this->getServiceLocator()->get('Entity\VariationCompletion')
            ->save($applicationStatusData);

        $this->getServiceLocator()->get('Entity\ApplicationTracking')
            ->save($applicationStatusData);

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

    public function getApplicationsForLicence($licenceId)
    {
        return $this->get(['licence' => $licenceId], $this->statusBundle);
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
            throw new DataServiceException('Is variation flag not found');
        }

        if ($data['isVariation']) {
            return RefData::APPLICATION_TYPE_VARIATION;
        }

        return RefData::APPLICATION_TYPE_NEW;
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

    public function getDataForUndertakings($id)
    {
        return $this->get($id, $this->undertakingsDataBundle);
    }

    public function getStatus($id)
    {
        return $this->get($id, $this->statusBundle)['status']['id'];
    }

    public function getSubmitSummaryData($id)
    {
        return $this->get($id, $this->statusBundle);
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

    public function getLicenceTotCommunityLicences($id)
    {
        $data = $this->get($id, $this->ocDataForVariationBundle);

        return $data['licence']['totCommunityLicences'];
    }

    public function getLicenceType($id)
    {
        return $this->get($id, $this->licenceTypeBundle);
    }

    public function getDataForPaymentSubmission($id)
    {
        return $this->get($id, $this->paymentSubmissionBundle);
    }

    public function getDataForFinancialEvidence($id)
    {
        return $this->get($id, $this->financialEvidenceBundle);
    }

    public function getTmHeaderData($id)
    {
        return $this->get($id, $this->tmHeaderBundle);
    }
}
