<?php

namespace Common\Service\Qa\Custom\Ecmt;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class PermitUsageFieldsetPopulatorFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): PermitUsageFieldsetPopulator
    {
        return new PermitUsageFieldsetPopulator(
            $container->get('QaRadioFieldsetPopulator'),
            $container->get('QaEcmtInfoIconAdder')
        );
    }

    /**
     * @deprecated
     */
    public function createService(ServiceLocatorInterface $serviceLocator): PermitUsageFieldsetPopulator
    {
        return $this->__invoke($serviceLocator, PermitUsageFieldsetPopulator::class);
    }
}
