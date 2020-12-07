<?php

/**
 * Licence Taxi Phv
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\FormService\Form\Lva;

/**
 * Licence Taxi Phv
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicenceTaxiPhv extends TaxiPhv
{
    /**
     * Make form alterations
     *
     * @param \Laminas\Form\Form $form
     * @return \Laminas\Form\Form
     */
    protected function alterForm($form)
    {
        parent::alterForm($form);

        $this->removeStandardFormActions($form);

        return $form;
    }
}
