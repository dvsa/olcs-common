<?php

/**
 * Transport Managers Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva;

/**
 * Transport Managers Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractTransportManagersController extends AbstractController
{
    /**
     * Transport managers section
     */
    public function indexAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {
            $this->postSave('transport_managers');
            return $this->completeSection('transport_managers');
        }

        $form = $this->getTransportManagersForm();

        $this->alterFormForLva($form);

        return $this->render('transport_managers', $form);
    }

    /**
     * Get transport maangers form
     *
     * @return \Zend\Form\Form
     */
    private function getTransportManagersForm()
    {
        return $this->getServiceLocator()->get('Helper\Form')->createForm('Lva\TransportManagers');
    }
}
