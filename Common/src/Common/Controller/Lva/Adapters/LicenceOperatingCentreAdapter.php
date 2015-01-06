<?php

/**
 * Licence Operating Centre Adapter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva\Adapters;

/**
 * Licence Operating Centre Adapter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicenceOperatingCentreAdapter extends AbstractOperatingCentreAdapter
{
    /**
     * Get extra document properties to save
     *
     * @return array
     */
    public function getDocumentProperties()
    {
        return array(
            'licence' => $this->getLicenceId()
        );
    }
}
