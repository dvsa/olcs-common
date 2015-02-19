<?php

/**
 * Common (aka Internal) Licence People Adapter
 *
 * @NOTE: currently identical and not abstracted at all
 * in common between all three LVAs
 *
 * This will change in the next two stories to be developed
 * OLCS-6542
 * OLCS-6543
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\Controller\Lva\Adapters;

use Zend\Form\Form;
use Common\Controller\Lva\Interfaces\PeopleAdapterInterface;

/**
 * Common (aka Internal) Licence People Adapter
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class LicencePeopleAdapter extends AbstractControllerAwareAdapter implements PeopleAdapterInterface
{

    public function addMessages($orgId)
    {
        return $this->getServiceLocator()->get('Lva\LicencePeople')->maybeAddVariationMessage(
            $this->getController(),
            $orgId
        );
    }

    public function alterFormForOrganisation(Form $form, $table, $orgId)
    {
    }

    public function alterFormForPartnership(Form $form, $table, $orgId)
    {
    }

    public function alterSoleTraderFormForOrganisation(Form $form, $orgId)
    {
    }

    public function alterAddOrEditFormForOrganisation(Form $form, $orgId)
    {
    }

    public function canModify($orgId)
    {
        return true;
    }
}
