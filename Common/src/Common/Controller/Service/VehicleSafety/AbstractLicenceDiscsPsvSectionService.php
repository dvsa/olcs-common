<?php

/**
 * Abstract Licence Discs Psv Section Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Service\VehicleSafety;

use Zend\Form\Form;

/**
 * Abstract Licence Discs Psv Section Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractLicenceDiscsPsvSectionService extends AbstractDiscsPsvSectionService
{
    public function alterForm(Form $form)
    {
        return parent::alterForm($this->getSectionService('Licence')->alterForm($form));
    }
}
