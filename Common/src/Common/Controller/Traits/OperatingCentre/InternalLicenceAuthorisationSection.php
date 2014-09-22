<?php

/**
 * Internal Licence Authorisation Section
 *
 * Internal - Licence Section
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Traits\OperatingCentre;

/**
 * Internal Licence Authorisation Section
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
trait InternalLicenceAuthorisationSection
{
    /**
     * Alter action form for Goods licences
     *
     * @param \Zend\Form\Form $form
     */
    protected function alterActionFormForGoods($form)
    {
    }

    /**
     * This method is implemented so we can re-use some other code, it doesn't do anything yet
     *
     * @param \Zend\Form\Form $form
     */
    protected function alterActionForm($form)
    {
        $form = $this->doAlterActionForm($form);

        $this->alterActionFormForLicence($form);

        return $form;
    }
}
