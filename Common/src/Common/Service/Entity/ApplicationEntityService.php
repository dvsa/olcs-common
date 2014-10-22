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
class ApplicationEntityService extends AbstractEntityService
{
    const APPLICATION_TYPE_NEW = 0;
    const APPLICATION_TYPE_VARIATION = 1;

    const APPLICATION_STATUS_NOT_SUBMITTED = 'apsts_not_submitted';

    /**
     * Define entity for default behaviour
     *
     * @var string
     */
    protected $entity = 'Application';

    /**
     * Holds the applications bundle
     *
     * @var array
     */
    private $applicationsForOrganisationBundle = array(
        'properties' => array(),
        'children' => array(
            'licences' => array(
                'properties' => array(
                    'id',
                    'licNo'
                ),
                'children' => array(
                    'applications' => array(
                        'properties' => array(
                            'id',
                            'createdOn',
                            'receivedDate',
                            'isVariation'
                        ),
                        'children' => array(
                            'status' => array(
                                'properties' => array(
                                    'id'
                                )
                            )
                        )
                    ),
                    'licenceType' => array(
                        'properties' => array(
                            'id',
                            'description'
                        )
                    ),
                    'status' => array(
                        'properties' => array(
                            'id',
                            'description'
                        )
                    )
                )
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
            'insolvencyConfirmation'
        ),
        'children' => array(
            'licence' => array(
                'properties' => array(
                    'niFlag',
                    'safetyInsVehicles',
                    'safetyInsTrailers',
                    'safetyInsVaries',
                    'tachographInsName'
                ),
                'children' => array(
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
                    )
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

    protected $authorisedVehiclesTotal = array(
        'properties' => array(
            'totAuthVehicles'
        )
    );

    /**
     * Document Bundle
     *
     * @var array
     */
    protected $documentBundle = array(
        'properties' => array(),
        'children' => array(
            'documents' => array(
                'properties' => array(
                    'id',
                    'version',
                    'filename',
                    'identifier',
                    'size'
                )
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

    /**
     * Get applications for a given organisation
     *
     * @param int $organisationId
     */
    public function getForOrganisation($organisationId)
    {
        return $this->getServiceLocator()->get('Helper\Rest')
            ->makeRestCall('Organisation', 'GET', $organisationId, $this->applicationsForOrganisationBundle);
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
            'status' => 'apsts_not_submitted',
            'isVariation' => false
        );

        $application = $this->save($applicationData);

        return array(
            'application' => $application['id'],
            'licence' => $licence['id']
        );
    }

    /**
     * Create the application, and the completion record
     *
     * @param array $data
     * @return array
     */
    public function save($data)
    {
        $application = parent::save($data);

        if (!isset($data['id'])) {
            $applicationCompletionData = [
                'application' => $application['id'],
            ];

            $this->getServiceLocator()->get('Entity\ApplicationCompletion')->save($applicationCompletionData);
        }

        return $application;
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

        if (isset($data['isVariation']) && $data['isVariation']) {
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

    public function getAuthorisedVehiclesTotal($id)
    {
        $data = $this->get($id, $this->authorisedVehiclesTotal);

        return $data['totAuthVehicles'];
    }

    public function getDocuments($id, $categoryName, $documentSubCategoryName)
    {
        $documentBundle = $this->documentBundle;

        $categoryService = $this->getServiceLocator()->get('category');

        $category = $categoryService->getCategoryByDescription($categoryName);
        $subCategory = $categoryService->getCategoryByDescription($documentSubCategoryName, 'Document');

        $documentBundle['children']['document']['criteria'] = array(
            'category' => $category['id'],
            'subCategory' => $subCategory['id']
        );

        $data = $this->get($id, $documentBundle);

        return $data['documents'];
    }

    public function getFinancialHistoryData($id)
    {
        return $this->get($id, $this->financialHistoryBundle);
    }
}
