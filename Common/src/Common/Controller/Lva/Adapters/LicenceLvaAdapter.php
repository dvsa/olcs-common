<?php

/**
 * Licence Lva Adapter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva\Adapters;

/**
 * Licence Lva Adapter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicenceLvaAdapter extends AbstractLvaAdapter
{
    public function getIdentifier()
    {
        $licence = $this->getController()->params('licence');

        if ($licence !== null) {
            return $licence;
        }

        $application = $this->getApplicationAdapter()->getIdentifier();

        return $this->getServiceLocator()->get('Entity\Application')->getLicenceIdForApplication($application);
    }
}
