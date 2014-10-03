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

        $licence = $this->getEntityService('Licence')->create($licenceData);

        $applicationData = array(
            'licence' => $licence['id'],
            'status' => 'apsts_not_submitted',
            'isVariation' => false
        );

        return $this->create($applicationData);
    }

    /**
     * Create the application, and the completion record
     *
     * @param array $data
     * @return array
     */
    public function create($data)
    {
        $application = parent::create($data);

        $applicationCompletionData = [
            'id' => $application['id'],
        ];

        $this->getEntityService('ApplicationCompletion')->create($applicationCompletionData);

        return $application;
    }
}
