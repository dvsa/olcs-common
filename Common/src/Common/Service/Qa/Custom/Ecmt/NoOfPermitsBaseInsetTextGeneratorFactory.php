<?php

namespace Common\Service\Qa\Custom\Ecmt;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class NoOfPermitsBaseInsetTextGeneratorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return NoOfPermitsBaseInsetTextGenerator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new NoOfPermitsBaseInsetTextGenerator(
            $serviceLocator->get('Helper\Translation'),
            $serviceLocator->get('ViewHelperManager')->get('currencyFormatter')
        );
    }
}
