<?php

namespace Common\Service\Qa\Custom\Common;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class WarningAdderFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return WarningAdder
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new WarningAdder(
            $serviceLocator->get('ViewHelperManager')->get('partial')
        );
    }
}
