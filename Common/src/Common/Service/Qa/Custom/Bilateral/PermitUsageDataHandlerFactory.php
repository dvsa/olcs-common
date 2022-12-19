<?php

namespace Common\Service\Qa\Custom\Bilateral;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class PermitUsageDataHandlerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): PermitUsageDataHandler
    {
        return new PermitUsageDataHandler(
            $container->get('QaCommonIsValidBasedWarningAdder'),
            $container->get('QaBilateralPermitUsageIsValidHandler')
        );
    }

    /**
     * @deprecated
     */
    public function createService(ServiceLocatorInterface $serviceLocator): PermitUsageDataHandler
    {
        return $this->__invoke($serviceLocator, PermitUsageDataHandler::class);
    }
}
