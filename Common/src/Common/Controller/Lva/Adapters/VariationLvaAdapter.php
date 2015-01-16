<?php

/**
 * Variation Lva Adapter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva\Adapters;

/**
 * Variation Lva Adapter
 *
 * @NOTE This could potentially extends the ApplicationLvaAdapter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationLvaAdapter extends AbstractLvaAdapter
{
    public function getIdentifier()
    {
        return $this->getApplicationAdapter()->getIdentifier();
    }
}
