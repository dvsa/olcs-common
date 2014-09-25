<?php

/**
 * Application Section Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Service;

/**
 * Application Section Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationSectionService extends AbstractSectionService
{
    /**
     * Application statuses
     */
    const APPLICATION_STATUS_NOT_YET_SUBMITTED = 'apsts_not_submitted';
    const APPLICATION_STATUS_CURTAILED = 'apsts_curtailed';
    const APPLICATION_STATUS_GRANTED = 'apsts_granted';
    const APPLICATION_STATUS_NOT_TAKEN_UP = 'apsts_ntu';
    const APPLICATION_STATUS_REFUSED = 'apsts_refused';
    const APPLICATION_STATUS_VALID = 'apsts_valid';
    const APPLICATION_STATUS_WITHDRAWN = 'apsts_withdrawn';
    const APPLICATION_STATUS_UNDER_CONSIDERATION = 'apsts_consideration';

    /**
     * Holds the licence section service
     *
     * @var \Common\Controller\Service\LicenceSectionService
     */
    private $licenceSectionService;

    /**
     * Holds the licence id
     *
     * @var int
     */
    private $licenceId;

    /**
     * This licence id data bundle
     *
     * @var array
     */
    private $licenceIdBundle = array(
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
     * Get a licence section service instance
     *
     * @return \Common\Controller\Service\LicenceSectionService
     */
    public function getLicenceSectionService()
    {
        if ($this->licenceSectionService === null) {
            $this->licenceSectionService = $this->createSectionService('Licence');
            $this->licenceSectionService->setIdentifier($this->getLicenceId());
        }

        return $this->licenceSectionService;
    }

    /**
     * Get licence id from the current application id
     *
     * @NOTE although this adds 1 extra rest call to the request, we will be able to re-use alot more logic going
     *  forward
     *
     * @return int
     */
    protected function getLicenceId()
    {
        if ($this->licenceId === null) {
            $data = $this->makeRestCall('Application', 'GET', $this->getIdentifier(), $this->licenceIdBundle);
            $this->licenceId = $data['licence']['id'];
        }

        return $this->licenceId;
    }
}
