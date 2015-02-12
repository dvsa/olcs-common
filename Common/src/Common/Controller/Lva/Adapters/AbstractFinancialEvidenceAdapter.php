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
     * @todo these will become a backend lookup following https://jira.i-env.net/browse/OLCS-6988
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
}
