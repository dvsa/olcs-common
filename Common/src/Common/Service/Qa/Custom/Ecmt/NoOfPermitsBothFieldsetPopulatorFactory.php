<?php

namespace Common\Service\Qa\Custom\Ecmt;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class NoOfPermitsBothFieldsetPopulatorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return NoOfPermitsBothFieldsetPopulator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new NoOfPermitsBothFieldsetPopulator(
            $serviceLocator->get('Helper\Translation'),
            $serviceLocator->get('QaEcmtNoOfPermitsBaseInsetTextGenerator'),
            $serviceLocator->get('QaCommonHtmlAdder')
        );
    }
}
