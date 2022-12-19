<?php

namespace Common\Service\Qa\Custom\Common;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class IsValidBasedWarningAdderFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): IsValidBasedWarningAdder
    {
        return new IsValidBasedWarningAdder(
            $container->get('QaCommonWarningAdder')
        );
    }

    /**
     * @deprecated
     */
    public function createService(ServiceLocatorInterface $serviceLocator): IsValidBasedWarningAdder
    {
        return $this->__invoke($serviceLocator, IsValidBasedWarningAdder::class);
    }
}
