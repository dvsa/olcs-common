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
use Common\Service\Entity\OrganisationEntityService;

/**
 * Common (aka Internal) Licence People Adapter
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class LicencePeopleAdapter extends AbstractControllerAwareAdapter implements PeopleAdapterInterface
{
    private $excludeTypes = [
        OrganisationEntityService::ORG_TYPE_SOLE_TRADER,
        OrganisationEntityService::ORG_TYPE_PARTNERSHIP
    ];

    public function addMessages($orgId)
    {
        return $this->getServiceLocator()->get('Lva\LicencePeople')->maybeAddVariationMessage(
            $this->getController(),
            $orgId
        );
    }

    public function alterFormForOrganisation(Form $form, $table, $orgId)
    {
        return $this->getServiceLocator()->get('Lva\People')->lockOrganisationForm($form, $table);
    }

    public function alterAddOrEditFormForOrganisation(Form $form, $orgType)
    {
        if (in_array($orgType, $this->excludeTypes)) {
            return;
        }

        return $this->getServiceLocator()->get('Lva\People')->lockPersonForm($form, $orgType);
    }

    public function canModify($orgId)
    {
        return true;
    }
}
