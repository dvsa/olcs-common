<?php

/**
 * Transport Manager Form
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\FormService\Form\Lva\TransportManager;

use Common\FormService\Form\Lva\AbstractLvaFormService;

/**
 * Transport Manager Form
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractTransportManager extends AbstractLvaFormService
{
    public function getForm()
    {
        $form = $this->getFormHelper()->createForm('Lva\TransportManagers');

        $this->alterForm($form);

        return $form;
    }

    /**
     * Make form alterations
     *
     * @param \Laminas\Form\Form $form
     * @return \Laminas\Form\Form
     */
    protected function alterForm($form)
    {
        $this->removeFormAction($form, 'save');
        $this->removeFormAction($form, 'cancel');

        return $form;
    }
}
