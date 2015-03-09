<?php

/**
 * Bus Processing Service
 */
namespace Common\Service\Processing;

use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Common\Service\Data\FeeTypeDataService;
use Common\Service\Entity\FeeEntityService;
use Common\Service\Entity\LicenceEntityService;

/**
 * Bus Processing Service
 */
class BusProcessingService implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * Maybe create a fee
     *
     * @param int $busRegId
     * @return boolean true if a fee was created, false otherwise (fee already exists)
     */
    public function maybeCreateFee($busRegId)
    {
        if (empty($busRegId)) {
            return false;
        }

        $fee = $this->getServiceLocator()->get('Entity\Fee')->getLatestFeeForBusReg($busRegId);

        if (!empty($fee)) {
            // existing fee, don't create one
            return false;
        }

        return $this->createFee($busRegId);
    }

    /**
     * Create a fee
     *
     * @param int $busRegId
     * @return boolean true if a fee was created
     */
    public function createFee($busRegId)
    {
        if (empty($busRegId)) {
            return false;
        }

        $busRegData = $this->getServiceLocator()->get('Entity\BusReg')->getDataForFees($busRegId);

        $busFeeType
            = !empty($busRegData['variationNo'])
                ? FeeTypeDataService::FEE_TYPE_BUSVAR : FeeTypeDataService::FEE_TYPE_BUSAPP;

        $trafficArea
            = !empty($busRegData['licence']['trafficArea']['isScotland'])
                ? $busRegData['licence']['trafficArea']['id'] : null;

        $feeType = $this->getServiceLocator()->get('Data\FeeType')->getLatest(
            $busFeeType,
            LicenceEntityService::LICENCE_CATEGORY_PSV,
            $busRegData['licence']['licenceType']['id'],
            $busRegData['receivedDate'],
            $trafficArea
        );

        if (empty($feeType)) {
            return false;
        }

        $feeData = array(
            'amount' => (float)$feeType['fixedValue'],
            'busReg' => $busRegId,
            'licence' => $busRegData['licence']['id'],
            'invoicedDate' => $busRegData['receivedDate'],
            'feeType' => $feeType['id'],
            'description' => $feeType['description'].' '.$busRegData['regNo'].' Variation '.$busRegData['variationNo'],
            'feeStatus' => FeeEntityService::STATUS_OUTSTANDING
        );

        $this->getServiceLocator()->get('Entity\Fee')->save($feeData);

        return true;
    }
}
