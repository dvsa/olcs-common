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
    protected $rates; // cache

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
     * @return float
     */
    public function getFirstVehicleRate($licenceType, $goodsOrPsv)
    {
        foreach ($this->getRates() as $rate) {
            if ($rate['goodsOrPsv']['id'] == $goodsOrPsv && $rate['licenceType']['id'] == $licenceType) {
                return (float) $rate['firstVehicleRate'];
            }
        }
    }

    /**
     * @param string $licenceType
     * @param string $goodsOrPsv
     * @return float
     */
    public function getAdditionalVehicleRate($licenceType, $goodsOrPsv)
    {
        foreach ($this->getRates() as $rate) {
            if ($rate['goodsOrPsv']['id'] == $goodsOrPsv && $rate['licenceType']['id'] == $licenceType) {
                return (float) $rate['additionalVehicleRate'];
            }
        }
    }

    protected function getRates()
    {
        // we only make one call to look up standing rates
        if (is_null($this->rates)) {
            $this->rates = $this->getServiceLocator()->get('Entity\FinancialStandingRate')
                ->getRatesInEffect();
        }
        return $this->rates;
    }
}
