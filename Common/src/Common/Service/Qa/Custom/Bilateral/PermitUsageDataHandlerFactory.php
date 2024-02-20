<?php

namespace Common\Service\Qa\Custom\Bilateral;

use Psr\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class PermitUsageDataHandlerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): PermitUsageDataHandler
    {
        return new PermitUsageDataHandler(
            $container->get('QaCommonIsValidBasedWarningAdder'),
            $container->get('QaBilateralPermitUsageIsValidHandler')
        );
    }
}
