<?php

/**
 * Common (aka Internal) Application People Adapter
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\Controller\Lva\Adapters;

use Zend\Form\Form;
use Common\Controller\Lva\Adapters\AbstractAdapter;

/**
 * Common (aka Internal) Application People Adapter
 *
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class ApplicationPeopleAdapter extends AbstractAdapter
{
    public function alterFormForOrganisation(Form $form, $orgId)
    {
        // no-op by default
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
