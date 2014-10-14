<?php

/**
 * Application Controller Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Traits\Lva;

use Common\Service\Entity\ApplicationService;

/**
 * Application Controller Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
trait ApplicationControllerTrait
{
    /**
     * Hook into the dispatch before the controller action is executed
     */
    protected function preDispatch()
    {
        $applicationId = $this->getApplicationId();

        if (!$this->isApplicationNew($applicationId)) {
            return $this->notFoundAction();
        }

        return $this->checkForRedirect($applicationId);
    }

    /**
     * Get application status
     *
     * @params int $applicationId
     * @return array
     */
    protected function getCompletionStatuses($applicationId)
    {
        return $this->getEntityService('ApplicationCompletion')->getCompletionStatuses($applicationId);
    }

    /**
     * Update application status
     *
     * @params int $applicationId
     * @params string $section
     */
    protected function updateCompletionStatuses($applicationId, $section)
    {
        if ($applicationId === null) {
            $applicationId = $this->getApplicationId();
        }
        $this->getEntityService('ApplicationCompletion')->updateCompletionStatuses($applicationId, $section);
    }

    /**
     * Check if the application is new
     *
     * @param int $applicationId
     * @return boolean
     */
    protected function isApplicationNew($applicationId)
    {
        return $this->getApplicationType($applicationId) === ApplicationService::APPLICATION_TYPE_NEW;
    }

    /**
     * Check if the application is variation
     *
     * @param int $applicationId
     * @return boolean
     */
    protected function isApplicationVariation($applicationId)
    {
        return $this->getApplicationType($applicationId) === ApplicationService::APPLICATION_TYPE_VARIATION;
    }

    /**
     * Get application type
     *
     * @param int $applicationId
     * @return int
     */
    protected function getApplicationType($applicationId)
    {
        return $this->getEntityService('Application')->getApplicationType($applicationId);
    }

    /**
     * Get application id
     *
     * @return int
     */
    protected function getApplicationId()
    {
        return $this->params('id');
    }

    /**
     * Get licence id
     *
     * @param int $applicationId
     * @return int
     */
    protected function getLicenceId($applicationId = null)
    {
        if ($applicationId === null) {
            $applicationId = $this->getApplicationId();
        }

        return $this->getEntityService('Application')->getLicenceIdForApplication($applicationId);
    }

    /**
     * Get type of licence data
     *
     * @return array
     */
    protected function getTypeOfLicenceData()
    {
        $licenceId = $this->getLicenceId($this->getApplicationId());

        return $this->getEntityService('Licence')->getTypeOfLicenceData($licenceId);
    }

    /**
     * Complete a section and potentially redirect to the next
     * one depending on the user's choice
     *
     * @return \Zend\Http\Response
     */
    protected function completeSection($section)
    {
        $this->updateCompletionStatuses($this->getApplicationId(), $section);

        $this->addSectionUpdatedMessage($section);

        if ($this->isButtonPressed('saveAndContinue')) {
            return $this->goToNextSection($section);
        }

        return $this->goToOverviewAfterSave();
    }
}
