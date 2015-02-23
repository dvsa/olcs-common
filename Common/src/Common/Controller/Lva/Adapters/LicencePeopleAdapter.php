<?php

/**
 * Common (aka Internal) Licence People Adapter
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\Controller\Lva\Adapters;

use Zend\Form\Form;
use Common\Controller\Lva\Interfaces\PeopleAdapterInterface;
use Common\Controller\Lva\Adapters\AbstractPeopleAdapter;

/**
 * Common (aka Internal) Licence People Adapter
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class LicencePeopleAdapter extends AbstractPeopleAdapter
{
    public function addMessages($orgType)
    {
        if ($this->isExceptionalType($orgType)) {
            return;
        }

        return $this->getServiceLocator()
            ->get('Lva\LicencePeople')
            ->addVariationMessage($this->getController());
    }

    public function alterFormForOrganisation(Form $form, $table, $orgId, $orgType)
    {
        if ($this->isExceptionalType($orgType)) {
            return;
        }

        return $this->getServiceLocator()->get('Lva\People')->lockOrganisationForm($form, $table);
    }

    public function alterAddOrEditFormForOrganisation(Form $form, $orgId, $orgType)
    {
        if ($this->isExceptionalType($orgType)) {
            return;
        }

        return $this->getServiceLocator()->get('Lva\People')->lockPersonForm($form, $orgType);
    }

    public function canModify($orgId)
    {
        return true;
    }
}
