<?php

/**
 * TaxiPhv Form
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Common\FormService\Form\Lva;

/**
 * TaxiPhv Form
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class TaxiPhv extends AbstractLvaFormService
{
    public function getForm()
    {
        $form = $this->getFormHelper()->createForm('Lva\TaxiPhv');

        $this->alterForm($form);

        return $form;
    }

    /**
     * Make form alterations
     *
     * @param \Zend\Form\Form $form
     * @return \Zend\Form\Form
     */
    protected function alterForm($form)
    {
        $this->removeFormAction($form, 'cancel');
        return $form;
    }
}
