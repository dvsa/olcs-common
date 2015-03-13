<?php

/**
 * Common (aka Internal) Licence People Adapter
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\Controller\Lva\Adapters;

use Zend\Form\Form;
use Common\Controller\Lva\Adapters\AbstractPeopleAdapter;

/**
 * Common (aka Internal) Licence People Adapter
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class LicencePeopleAdapter extends AbstractPeopleAdapter
{
    public function addMessages($orgId, $id)
    {
        if ($this->isExceptionalOrganisation($orgId)) {
            return;
        }

        return $this->getServiceLocator()->get('Lva\Variation')->addVariationMessage($id);
    }

    public function alterFormForOrganisation(Form $form, $table, $orgId)
    {
        if ($this->isExceptionalOrganisation($orgId)) {
            return;
        }

        return $this->getServiceLocator()->get('Lva\People')->lockOrganisationForm($form, $table);
    }

    public function alterAddOrEditFormForOrganisation(Form $form, $orgId)
    {
        if ($this->isExceptionalOrganisation($orgId)) {
            return;
        }

        return $this->getServiceLocator()->get('Lva\People')->lockPersonForm(
            $form,
            $this->getOrganisationType($orgId)
        );
    }

    public function canModify($orgId)
    {
        // internally we can modify simple orgs only
        return $this->isExceptionalOrganisation($orgId);
    }
}
