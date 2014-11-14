<?php

/**
 * Abstract  Undertakings Controller
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\Controller\Lva;

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
}
