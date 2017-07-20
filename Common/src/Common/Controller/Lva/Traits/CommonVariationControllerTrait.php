<?php

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
     *
     * @return \Zend\View\Model\ViewModel|null|\Zend\Http\Response
     */
    protected function preDispatch()
    {
        if ($this->isApplicationNew()) {
            return $this->notFoundAction();
        }

        return $this->checkForRedirect($this->getApplicationId());
    }

    /**
     * Post Save
     *
     * @param string $section Section
     *
     * @return void
     */
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
     * @param string $currentSection Current Section
     *
     * @return \Zend\Http\Response
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

    /**
     * Alter Table.  This overrides the AbstractFinancialHistoryController method.
     * This is the reason why i placed the label update logic into a seperate method
     * to avoid duplication of code.
     *
     * @param Form  $form Form
     * @param array $data Form Data
     *
     * @return Form
     */
    protected function alterFormForLva(Form $form, $data = null)
    {
        $form = $this->updateInsolvencyConfirmationLabel($form, $data);
        return $this->getServiceLocator()->get('FormServiceManager')->get('lva-variation')->alterForm($form);
    }
}
