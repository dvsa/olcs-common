<?php

/**
 * Common (aka Internal) Application People Adapter
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
use Common\Controller\Lva\Adapters\AbstractAdapter;

/**
 * Common (aka Internal) Variation People Adapter
 *
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class VariationPeopleAdapter extends AbstractAdapter
{
    public function alterFormForOrganisation(Form $form, $orgId)
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
