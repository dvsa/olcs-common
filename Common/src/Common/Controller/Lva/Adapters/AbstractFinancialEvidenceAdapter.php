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
    // these values will be retrieved via SystemParameterEntityService
    // following https://jira.i-env.net/browse/OLCS-6988
    const RATE_GOODS_FIRST_STANDARD        = 7000;
    const RATE_GOODS_FIRST_RESTRICTED      = 3100;
    const RATE_GOODS_ADDITIONAL_STANDARD   = 3900;
    const RATE_GOODS_ADDITIONAL_RESTRICTED = 1700;
    const RATE_PSV_FIRST_STANDARD          = 8000;
    const RATE_PSV_FIRST_RESTRICTED        = 4100;
    const RATE_PSV_ADDITIONAL_STANDARD     = 4900;
    const RATE_PSV_ADDITIONAL_RESTRICTED   = 2700;

    /**
     * @param int $id
     * @return array
     */
    abstract public function getFormData($id);

    /**
     * @param int $id
     * @return int
     */
    abstract public function getTotalNumberOfAuthorisedVehicles($id);

    /**
     * @param int $id
     * @return int Required finance amount
     */
    abstract public function getRequiredFinance($id);

    /**
     * @param int $id
     * @return array
     */
    abstract public function getDocuments($id);

    /**
     * @param array $file
     * @param int $id
     * @return array
     */
    abstract public function getUploadMetaData($file, $id);

    /**
     * @param int $id
     * @return array
     */
    abstract public function getRatesForView($id);

    /**
     * @param Common\Form\Form
     * @return void
     */
    public function alterFormForLva($form)
    {
        // no-op by default, can be extended
    }

    /**
     * @param string $licenceType
     * @param string $goodsOrPsv
     * @return int
     *
     * @todo these will become a system parameter lookup following https://jira.i-env.net/browse/OLCS-6988
     */
    public function getFirstVehicleRate($licenceType, $goodsOrPsv)
    {
        switch ($licenceType) {
            case Licence::LICENCE_TYPE_RESTRICTED:
                if ($goodsOrPsv === Licence::LICENCE_CATEGORY_PSV) {
                    return self::RATE_PSV_FIRST_RESTRICTED;
                }
                return self::RATE_GOODS_FIRST_RESTRICTED;
            default:
                if ($goodsOrPsv === Licence::LICENCE_CATEGORY_PSV) {
                    return self::RATE_PSV_FIRST_STANDARD;
                }
                return self::RATE_GOODS_FIRST_STANDARD;
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
                    return self::RATE_PSV_ADDITIONAL_RESTRICTED;
                }
                return self::RATE_GOODS_ADDITIONAL_RESTRICTED;
            default:
                if ($goodsOrPsv === Licence::LICENCE_CATEGORY_PSV) {
                    return self::RATE_PSV_ADDITIONAL_STANDARD;
                }
                return self::RATE_GOODS_ADDITIONAL_STANDARD;
        }
    }
}
