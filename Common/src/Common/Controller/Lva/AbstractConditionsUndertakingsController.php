<?php

/**
 * Abstract Conditions Undertakings Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva;

/**
 * Abstract Conditions Undertakings Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractConditionsUndertakingsController extends AbstractController
{
    /**
     * Conditions Undertakings section
     */
    public function indexAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {
            $this->postSave('conditions_undertakings');
            return $this->completeSection('conditions_undertakings');
        }

        $form = $this->getForm();

        $this->alterFormForLva($form);

        return $this->render('conditions_undertakings', $form);
    }

    /**
     * Get conditions undertakings form
     *
     * @return \Zend\Form\Form
     */
    private function getForm()
    {
        return $this->getServiceLocator()->get('Helper\Form')->createForm('Lva\ConditionsUndertakings');
    }
}
