<?php

/**
 * Variation Controller Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva\Traits;

use Zend\Form\Form;

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
        if ($this->isApplicationNew()) {
            return $this->notFoundAction();
        }

        return $this->checkForRedirect($this->getApplicationId());
    }

    protected function postSave($section)
    {
        $this->getServiceLocator()->get('Processing\VariationSection')
            ->setApplicationId($this->getApplicationId())
            ->clearCache()
            ->completeSection($section);
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

    protected function alterFormForLva(Form $form)
    {
        return $this->getServiceLocator()->get('FormServiceManager')->get('lva-variation')->alterForm($form);
    }
}
