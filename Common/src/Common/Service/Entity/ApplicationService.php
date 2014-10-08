<?php

/**
 * Application Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Entity;

use Common\Service\Entity\LicenceService;

/**
 * Application Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationService extends AbstractEntityService
{
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
            ),
            'licence' => array(
                'properties' => array(
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
     * Get applications for a given organisation
     *
     * @param int $organisationId
     */
    public function getForOrganisation($organisationId)
    {
        return $this->getHelperService('RestHelper')
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
            'status' => LicenceService::LICENCE_STATUS_NEW,
            'organisation' => $organisationId,
        );

        $licence = $this->getEntityService('Licence')->save($licenceData);

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

        $applicationCompletionData = [
            'application' => $application['id'],
        ];

        $this->getEntityService('ApplicationCompletion')->save($applicationCompletionData);

        return $application;
    }

    /**
     * Check whether the application belongs to the organisation
     *
     * @param int $applicationId
     * @param int $orgId
     * @return boolean
     */
    public function doesBelongToOrganisation($applicationId, $orgId)
    {
        $data = $this->getHelperService('RestHelper')
            ->makeRestCall('Application', 'GET', $applicationId, $this->doesBelongToOrgBundle);

        return (isset($data['licence']['organisation']['id']) && $data['licence']['organisation']['id'] == $orgId);
    }

    /**
     * Get data for overview
     *
     * @param int $applicationId
     * @return array
     */
    public function getOverview($applicationId)
    {
        return $this->getHelperService('RestHelper')
            ->makeRestCall('Application', 'GET', $applicationId, $this->overviewBundle);
    }

    /**
     * Get licence for the given application id
     *
     * @param int $applicationId
     * @param array $bundle
     * @return array
     */
    public function getLicenceIdForApplication($applicationId)
    {
        $data = $this->getHelperService('RestHelper')
            ->makeRestCall($this->entity, 'GET', $applicationId, $this->licenceIdForApplicationBundle);

        return $data['licence']['id'];
    }
}
