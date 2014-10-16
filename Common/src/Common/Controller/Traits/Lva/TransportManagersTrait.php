<?php

/**
 * Transport Managers Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Traits\Lva;

/**
 * Transport Managers Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
trait TransportManagersTrait
{
    /**
     * Transport managers section
     */
    public function indexAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {
            return $this->completeSection('transport_managers');
        }

        $form = $this->getTransportManagersForm();

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
