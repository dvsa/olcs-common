<?php

namespace Common\FormService\Form\Lva;

/**
 * PSV Vehicles Form
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class PsvVehicles extends AbstractLvaFormService
{
    protected $showShareInfo = false;

    public function getForm()
    {
        $form = $this->getFormHelper()->createForm('Lva\PsvVehicles');

        $this->alterForm($form);

        if ($this->showShareInfo === false) {
            $this->getFormHelper()->remove($form, 'shareInfo');
        }

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
