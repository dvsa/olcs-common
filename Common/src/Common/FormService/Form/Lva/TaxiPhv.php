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
     * @param \Laminas\Form\Form $form
     * @return \Laminas\Form\Form
     */
    protected function alterForm($form)
    {
        $this->removeFormAction($form, 'cancel');
        return $form;
    }
}
