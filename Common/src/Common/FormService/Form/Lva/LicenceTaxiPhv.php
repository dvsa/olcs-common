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
     * @param \Zend\Form\Form $form
     * @return \Zend\Form\Form
     */
    protected function alterForm($form)
    {
        parent::alterForm($form);

        $this->removeStandardFormActions($form);

        return $form;
    }
}
