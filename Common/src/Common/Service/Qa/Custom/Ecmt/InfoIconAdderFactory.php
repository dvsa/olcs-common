<?php

namespace Common\Service\Qa\Custom\Ecmt;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class InfoIconAdderFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return InfoIconAdder
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new InfoIconAdder(
            $serviceLocator->get('Helper\Translation'),
            $serviceLocator->get('QaCommonHtmlAdder')
        );
    }
}
