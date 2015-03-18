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
    public function hasChangedRegisteredAddress($orgId, $address);

    public function hasChangedNatureOfBusiness($orgId, $natureOfBusiness);

    public function hasChangedSubsidiaryCompany($id, $data);

    public function postSave($data);

    public function postCrudSave($action, $data);
}
