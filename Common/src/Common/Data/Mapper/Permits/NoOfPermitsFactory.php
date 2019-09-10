<?php

namespace Common\Data\Mapper\Permits;

use Common\RefData;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class NoOfPermitsFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return NoOfPermits
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $noOfPermits = new NoOfPermits();

        $noOfPermits->registerMapper(
            RefData::IRHP_BILATERAL_PERMIT_TYPE_ID,
            $serviceLocator->get(BilateralNoOfPermits::class)
        );

        $noOfPermits->registerMapper(
            RefData::IRHP_MULTILATERAL_PERMIT_TYPE_ID,
            $serviceLocator->get(MultilateralNoOfPermits::class)
        );

        return $noOfPermits;
    }
}
