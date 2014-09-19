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
        return $form;
    }
}
