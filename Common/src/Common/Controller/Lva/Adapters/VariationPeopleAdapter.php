<?php

/**
 * Common (aka Internal) Application People Adapter
 *
 * @NOTE: currently identical and not abstracted at all
 * in common between all three LVAs
 *
 * This will change in the next story to be developed
 * OLCS-6543
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\Controller\Lva\Adapters;

use Zend\Form\Form;
use Common\Controller\Lva\Adapters\AbstractAdapter;
use Common\Controller\Lva\Interfaces\PeopleAdapterInterface;

/**
 * Common (aka Internal) Variation People Adapter
 *
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class VariationPeopleAdapter extends AbstractAdapter implements PeopleAdapterInterface
{
    public function addMessages($orgType)
    {
    }

    public function alterFormForOrganisation(Form $form, $table, $orgId, $orgType)
    {
        // @TODO: if the type === partnership, return early since internal
        // can always edit these org types

        // otherwise go ahead with variation logic @TODO
    }

    public function alterAddOrEditFormForOrganisation(Form $form, $orgId, $orgType)
    {
    }

    public function canModify($orgId)
    {
        return true;
    }
}
