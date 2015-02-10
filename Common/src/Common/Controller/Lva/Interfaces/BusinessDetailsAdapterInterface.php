<?php

/**
 * Business Details Adapter Interface
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\Controller\Lva\Interfaces;

use Zend\Form\Form;

/**
 * Business Details Adapter Interface
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
interface BusinessDetailsAdapterInterface
{
    public function alterFormForOrganisation(Form $Form, $orgId);

    public function postSave($data);

    public function postCrudSave($data);
}
