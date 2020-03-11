<?php

namespace Common\Controller\Lva\Traits;

use Dvsa\Olcs\Transfer\Query\Application\Application;
use Zend\Http\Response as HttpResponse;

/**
 * Application Controller Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
trait CommonApplicationControllerTrait
{
    use EnabledSectionTrait;

    /**
     * Runs if action is not found
     *
     * @return mixed
     */
    abstract public function notFoundAction();

    /**
     * Checks for redirect
     *
     * @param int $lvaId lva id
     *
     * @return mixed
     */
    abstract protected function checkForRedirect($lvaId);

    /**
     * Hook into the dispatch before the controller action is executed
     *
     * @return HttpResponse|null
     */
    protected function preDispatch()
    {
        if ($this->isApplicationVariation()) {
            return $this->notFoundAction();
        }

        return $this->checkForRedirect($this->getApplicationId());
    }

    /**
     * Get application status
     *
     * @param int $applicationId application id
     *
     * @return array
     */
    protected function getCompletionStatuses($applicationId)
    {
        return $this->getServiceLocator()->get('Entity\ApplicationCompletion')->getCompletionStatuses($applicationId);
    }

    /**
     * Update application status
     *
     * @param int    $applicationId application id
     * @param string $section       section
     *
     * @return void
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
     * @return boolean
     */
    protected function isApplicationNew()
    {
        return !$this->isApplicationVariation();
    }

    /**
     * Check if the application is variation
     *
     * @return boolean
     */
    protected function isApplicationVariation()
    {
        $data = $this->fetchDataForLva();

        return $data['isVariation'];
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
     * @param int|null $applicationId application id
     *
     * @return int
     */
    protected function getLicenceId($applicationId = null)
    {
        if ($applicationId === null) {
            $applicationId = $this->getApplicationId();
        }

        $response = $this->handleQuery(Application::create(['id' => $applicationId]));

        $data = $response->getResult();

        return $data['licence']['id'];
    }

    /**
     * Complete a section and potentially redirect to the next
     * one depending on the user's choice
     *
     * @param string $section section
     * @param array  $prg     prg
     *
     * @return HttpResponse
     */
    protected function completeSection($section, $prg = [])
    {

        if ($this->isButtonPressed('saveAndContinue', $prg)) {
            //undertakings section works differently, we return to the overview as there's no "next section"
            if ($section === 'undertakings') {
                return $this->goToOverview($this->getApplicationId());
            }

            return $this->goToNextSection($section);
        }

        return $this->goToOverviewAfterSave();
    }

    /**
     * post save
     *
     * @param string $section section
     *
     * @return void
     */
    protected function postSave($section)
    {
        $this->updateCompletionStatuses($this->getApplicationId(), $section);
    }

    /**
     * Redirect to the next section
     *
     * @param string $currentSection current section
     *
     * @return HttpResponse
     */
    protected function goToNextSection($currentSection)
    {
        $data = $this->getServiceLocator()->get('Entity\Application')
            ->getOverview($this->getApplicationId());

        $sectionStatus = $this->setEnabledAndCompleteFlagOnSections(
            $this->getAccessibleSections(false),
            $data['applicationCompletion']
        );

        $sections = array_keys($sectionStatus);

        $index = array_search($currentSection, $sections);

        // If there is no next section, or the next section is disabled
        if (!isset($sections[$index + 1]) || !$sectionStatus[$sections[$index + 1]]['enabled']) {
            return $this->goToOverview($this->getApplicationId());
        } else {
            return $this->redirect()
                ->toRouteAjax(
                    'lva-' . $this->lva . '/' . $sections[$index + 1],
                    array($this->getIdentifierIndex() => $this->getApplicationId())
                );
        }
    }
}
