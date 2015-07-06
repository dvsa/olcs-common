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
    public function addMessages()
    {
        if ($this->isExceptionalOrganisation()) {
            return;
        }

        return $this->getServiceLocator()->get('Lva\Variation')->addVariationMessage($this->getLicenceId());
    }

    public function alterFormForOrganisation(Form $form, $table)
    {
        if ($this->isExceptionalOrganisation()) {
            return;
        }

        return $this->getServiceLocator()->get('Lva\People')->lockOrganisationForm($form, $table);
    }

    public function alterAddOrEditFormForOrganisation(Form $form)
    {
        if ($this->isExceptionalOrganisation()) {
            return;
        }

        return $this->getServiceLocator()->get('Lva\People')->lockPersonForm($form, $this->getOrganisationType());
    }

    public function canModify()
    {
        // internally we can modify simple orgs only
        return $this->isExceptionalOrganisation();
    }
}
