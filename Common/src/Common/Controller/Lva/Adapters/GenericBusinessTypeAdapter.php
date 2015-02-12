<?php

/**
 * Generic Business Type Adapter
 *
 * Shared internally across Licences, Variations and Applications
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\Controller\Lva\Adapters;

use Zend\Form\Form;
use Common\Controller\Lva\Interfaces\BusinessTypeAdapterInterface;

/**
 * Generic Business Type Adapter
 *
 * Shared internally across Licences, Variations and Applications
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class GenericBusinessTypeAdapter extends AbstractAdapter implements BusinessTypeAdapterInterface
{
    public function alterFormForOrganisation(Form $form, $orgId)
    {
        // no-op
    }
}
