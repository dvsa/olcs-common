<?php

namespace Common\Service\Qa\Custom\Ecmt;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class NoOfPermitsEitherFieldsetPopulatorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return NoOfPermitsEitherFieldsetPopulator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new NoOfPermitsEitherFieldsetPopulator(
            $serviceLocator->get('Helper\Translation'),
            $serviceLocator->get('QaEcmtNoOfPermitsBaseInsetTextGenerator'),
            $serviceLocator->get('QaCommonHtmlAdder')
        );
    }
}
