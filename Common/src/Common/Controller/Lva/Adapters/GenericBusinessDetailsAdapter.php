<?php

/**
 * Generic Business Details Adapter
 *
 * Shared internally across Licences, Variations and Applications
 *
 * Note that so far internally every single implementation is a no-op
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\Controller\Lva\Adapters;

use Zend\Form\Form;
use Common\Controller\Lva\Interfaces\BusinessDetailsAdapterInterface;

/**
 * Generic Business Details Adapter
 *
 * Shared internally across Licences, Variations and Applications
 *
 * Note that so far internally every single implementation is a no-op
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class GenericBusinessDetailsAdapter extends AbstractAdapter implements BusinessDetailsAdapterInterface
{
    public function alterFormForOrganisation(Form $form, $orgId)
    {
        // no-op
    }

    public function hasChangedRegisteredAddress($orgId, $address)
    {
        // no-op
    }

    public function hasChangedNatureOfBusiness($orgId, $natureOfBusiness)
    {
        // no-op
    }

    public function hasChangedSubsidiaryCompany($id, $data)
    {
        // no-op
    }

    public function postSave($data)
    {
        // no-op
    }

    public function postCrudSave($action, $data)
    {
        // no-op
    }
}
