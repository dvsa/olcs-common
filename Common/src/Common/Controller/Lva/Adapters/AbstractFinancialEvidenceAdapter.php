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
abstract class AbstractFinancialEvidenceAdapter extends AbstractControllerAwareAdapter implements
FinancialEvidenceAdapterInterface
{
    abstract public function getTotalNumberOfAuthorisedVehicles($id);

    abstract public function getRequiredFinance($id);

    public function alterFormForLva($form)
    {
        // no-op by default, can be extended
    }

    abstract public function getDocuments($id);

    abstract public function getUploadMetaData($file, $id);

    /**
     * @param string $licenceType
     * @param string $goodsOrPsv
     * @return int
     *
     * @todo these will come from a db lookup eventually, but they are hardcoded
     * for now, see table defined in https://jira.i-env.net/browse/OLCS-2222
     */
    public function getFirstVehicleRate($licenceType, $goodsOrPsv)
    {
        switch ($licenceType) {
            case Licence::LICENCE_TYPE_RESTRICTED:
                if ($goodsOrPsv === Licence::LICENCE_CATEGORY_PSV) {
                    return 4100;
                }
                return 3100;
            default:
                if ($goodsOrPsv === Licence::LICENCE_CATEGORY_PSV) {
                    return 8000;
                }
                return 7000;
        }
    }

    /**
     * @param string $licenceType
     * @param string $goodsOrPsv
     * @return int
     */
    public function getAdditionalVehicleRate($licenceType, $goodsOrPsv)
    {
        switch ($licenceType) {
            case Licence::LICENCE_TYPE_RESTRICTED:
                if ($goodsOrPsv === Licence::LICENCE_CATEGORY_PSV) {
                    return 2700;
                }
                return 1700;
            default:
                if ($goodsOrPsv === Licence::LICENCE_CATEGORY_PSV) {
                    return 4900;
                }
                return 3900;
        }
    }

    /**
     * @return array
     */
    public function getRatesForView()
    {
        return [
            'standardFirst' => $this->getFirstVehicleRate(
                Licence::LICENCE_TYPE_STANDARD_NATIONAL,
                Licence::LICENCE_CATEGORY_GOODS_VEHICLE
            ),
            'standardAdditional' => $this->getAdditionalVehicleRate(
                Licence::LICENCE_TYPE_STANDARD_NATIONAL,
                Licence::LICENCE_CATEGORY_GOODS_VEHICLE
            ),
            'restrictedFirst' => $this->getFirstVehicleRate(
                Licence::LICENCE_TYPE_STANDARD_NATIONAL,
                Licence::LICENCE_CATEGORY_GOODS_VEHICLE
            ),
            'restrictedAdditional' => $this->getAdditionalVehicleRate(
                Licence::LICENCE_TYPE_STANDARD_NATIONAL,
                Licence::LICENCE_CATEGORY_GOODS_VEHICLE
            ),
        ];
    }
}
