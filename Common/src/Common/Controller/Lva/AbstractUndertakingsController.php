<?php

/**
 * Abstract  Undertakings Controller
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\Controller\Lva;

use Zend\Form\Form;

/**
 * Abstract  Undertakings Controller
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
abstract class AbstractUndertakingsController extends AbstractController
{
    /**
     *  Undertakings section
     */
    public function indexAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {
            $this->postSave('undertakings');
            return $this->completeSection('undertakings');
        }

        $form = $this->getForm();

        $this->alterFormForLva($form);

        return $this->render('undertakings', $form);
    }

    /**
     * Get undertakings form
     *
     * @return \Zend\Form\Form
     */
    private function getForm()
    {
        return $this->getServiceLocator()->get('Helper\Form')->createForm('Lva\Undertakings');
    }

    /**
     * @param \Zend\Form\Form
     */
    protected function alterFormForLva(Form $form)
    {
        // @todo push this into concretes with correct values
        $form->get('declarationsAndUndertakings')->get('undertakings')->setValue('markup-undertakings-psv356');
        $form->get('declarationsAndUndertakings')->get('declarations')->setValue('markup-declarations-psv356');
    }
}
