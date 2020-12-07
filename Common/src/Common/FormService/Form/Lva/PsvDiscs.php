<?php

/**
 * PSV Discs Form
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Common\FormService\Form\Lva;

/**
 * PSV Discs Form
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class PsvDiscs extends AbstractLvaFormService
{
    public function getForm()
    {
        $form = $this->getFormHelper()->createForm('Lva\PsvDiscs');

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
