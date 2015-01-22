<?php

/**
 * Variation Controller Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva\Traits;

/**
 * Variation Controller Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
trait CommonVariationControllerTrait
{
    /**
     * Hook into the dispatch before the controller action is executed
     */
    protected function preDispatch()
    {
        $applicationId = $this->getApplicationId();

        if (!$this->isApplicationVariation($applicationId)) {
            return $this->notFoundAction();
        }

        return $this->checkForRedirect($applicationId);
    }

    protected function postSave($section)
    {
        $this->getServiceLocator()->get('Processing\VariationSection')
            ->completeSection($this->getApplicationId(), $section);
    }

    /**
     * Redirect to the next section
     *
     * @param string $currentSection
     */
    protected function goToNextSection($currentSection)
    {
        $sections = $this->getAccessibleSections();

        $index = array_search($currentSection, $sections);

        // If there is no next section, or the next section is disabled
        if (!isset($sections[$index + 1])) {
            return $this->goToOverview($this->getApplicationId());
        } else {
            $params = array($this->getIdentifierIndex() => $this->getApplicationId());
            return $this->redirect()
                ->toRouteAjax('lva-variation/' . $sections[$index + 1], $params);
        }
    }
}
