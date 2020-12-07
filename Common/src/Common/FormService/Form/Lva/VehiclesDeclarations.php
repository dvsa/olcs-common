<?php

/**
 * Vehicles Declarations Form
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Common\FormService\Form\Lva;

use Common\FormService\Form\AbstractFormService;

/**
 * Vehicles Declarations Form
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class VehiclesDeclarations extends AbstractFormService
{
    public function getForm()
    {
        $form = $this->getFormHelper()->createForm('Lva\VehiclesDeclarations');

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
        return $form;
    }
}
