<?php

/**
 * Abstract Financial Evidence Adapter
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Common\Controller\Lva\Adapters;

use Common\Controller\Lva\Interfaces\FinancialEvidenceAdapterInterface;
use Common\Service\Entity\LicenceEntityService as Licence;

/**
 * Abstract Financial Evidence Adapter
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
abstract class AbstractFinancialEvidenceAdapter extends AbstractAdapter implements FinancialEvidenceAdapterInterface
{
    abstract public function getTotalNumberOfAuthorisedVehicles($id);

    abstract public function getRequiredFinance($id);

    public function alterFormForLva($form)
    {
        // no-op, can be extended
    }

    /**
     * @param string $licenceType
     * @return int
     * @todo these will come from db eventually, but OLCS-2222 specifies they
     * are hard-coded for now
     */
    public function getFirstVehicleRate($licenceType)
    {
        switch ($licenceType) {
            case Licence::LICENCE_TYPE_RESTRICTED:
                return 3100;
            default:
                // LICENCE_TYPE_SPECIAL_RESTRICTED is n/a
                return 7000;
        }
    }

    /**
     * @param string $licenceType
     * @return int
     */
    public function getAdditionalVehicleRate($licenceType)
    {
        switch ($licenceType) {
            case Licence::LICENCE_TYPE_RESTRICTED:
                return 1700;
            default:
                // LICENCE_TYPE_SPECIAL_RESTRICTED is n/a
                return 3900;
        }
    }
}
