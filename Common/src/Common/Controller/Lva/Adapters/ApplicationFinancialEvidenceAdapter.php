<?php

/**
 * Application Type Of Licence Adapter
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Common\Controller\Lva\Adapters;

/**
 * Application Financial Evidence Adapter
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class ApplicationFinancialEvidenceAdapter extends AbstractFinancialEvidenceAdapter
{
    /**
     * Get the total vehicle authority which includes the vehicles on this
     * application and across all other licence records where status of the
     * licence is:
     *   Under consideration
     *   Granted
     *   Valid
     *   Suspended
     *   Curtailed
     *
     * @return int
     */
    public function getTotalNumberOfAuthorisedVehicles($applicationId)
    {
        return $this->getServiceLocator()->get('Entity\Application')
            ->getTotalVehicleAuthorisationIncLicence($applicationId);
    }

    public function getRequiredFinance($id)
    {
        return 12345.67;
    }
}
