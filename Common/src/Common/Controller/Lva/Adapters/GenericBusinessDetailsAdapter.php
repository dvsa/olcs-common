<?php

/**
 * Generic Business Details Adapter
 *
 * Shared internally across Licences, Variations and Applications
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
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class GenericBusinessDetailsAdapter extends AbstractAdapter implements BusinessDetailsAdapterInterface
{
    public function alterFormForOrganisation(Form $form, $orgId)
    {
        // no-op
    }

    public function postSave($data)
    {
        // no-op
    }

    public function postCrudSave($data)
    {
        // no-op
    }
}
