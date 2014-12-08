<?php

/**
 * Application Controller Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva\Traits;

use Common\Service\Entity\ApplicationEntityService;

/**
 * Application Controller Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
trait CommonApplicationControllerTrait
{
    use EnabledSectionTrait;

    abstract protected function notFoundAction();
    abstract protected function checkForRedirect($lvaId);

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
        return $this->getServiceLocator()->get('Entity\ApplicationCompletion')->getCompletionStatuses($applicationId);
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

        $this->getServiceLocator()->get('Entity\ApplicationCompletion')
            ->updateCompletionStatuses($applicationId, $section);
    }

    /**
     * Check if the application is new
     *
     * @param int $applicationId
     * @return boolean
     */
    protected function isApplicationNew($applicationId)
    {
        return $this->getApplicationType($applicationId) === ApplicationEntityService::APPLICATION_TYPE_NEW;
    }

    /**
     * Check if the application is variation
     *
     * @param int $applicationId
     * @return boolean
     */
    protected function isApplicationVariation($applicationId)
    {
        return $this->getApplicationType($applicationId) === ApplicationEntityService::APPLICATION_TYPE_VARIATION;
    }

    /**
     * Get application type
     *
     * @param int $applicationId
     * @return int
     */
    protected function getApplicationType($applicationId)
    {
        return $this->getServiceLocator()->get('Entity\Application')->getApplicationType($applicationId);
    }

    /**
     * Get application id
     *
     * @return int
     */
    protected function getApplicationId()
    {
        return $this->getIdentifier();
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

        return $this->getServiceLocator()->get('Entity\Application')->getLicenceIdForApplication($applicationId);
    }

    /**
     * Complete a section and potentially redirect to the next
     * one depending on the user's choice
     *
     * @return \Zend\Http\Response
     */
    protected function completeSection($section)
    {
        $this->addSectionUpdatedMessage($section);

        if ($this->isButtonPressed('saveAndContinue')) {
            return $this->goToNextSection($section);
        }

        return $this->goToOverviewAfterSave();
    }

    protected function postSave($section)
    {
        $this->updateCompletionStatuses($this->getApplicationId(), $section);
    }

    /**
     * Redirect to the next section
     *
     * @param string $currentSection
     */
    protected function goToNextSection($currentSection)
    {
        $data = $this->getServiceLocator()->get('Entity\Application')
            ->getOverview($this->getApplicationId());

        $sectionStatus = $this->setEnabledAndCompleteFlagOnSections(
            $this->getAccessibleSections(false),
            $data['applicationCompletions'][0]
        );

        $sections = array_keys($sectionStatus);

        $index = array_search($currentSection, $sections);

        // If there is no next section, or the next section is disabled
        if (!isset($sections[$index + 1]) || !$sectionStatus[$sections[$index + 1]]['enabled']) {
            return $this->goToOverview($this->getApplicationId());
        } else {
            return $this->redirect()
                ->toRoute(
                    'lva-' . $this->lva . '/' . $sections[$index + 1],
                    array($this->getIdentifierIndex() => $this->getApplicationId())
                );
        }
    }
}
