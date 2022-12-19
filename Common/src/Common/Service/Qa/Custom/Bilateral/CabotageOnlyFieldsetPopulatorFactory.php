<?php

namespace Common\Service\Qa\Custom\Bilateral;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class CabotageOnlyFieldsetPopulatorFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): CabotageOnlyFieldsetPopulator
    {
        return new CabotageOnlyFieldsetPopulator(
            $container->get('Helper\Translation'),
            $container->get('QaBilateralYesNoWithMarkupForNoPopulator'),
            $container->get('QaBilateralStandardYesNoValueOptionsGenerator')
        );
    }

    /**
     * @deprecated
     */
    public function createService(ServiceLocatorInterface $serviceLocator): CabotageOnlyFieldsetPopulator
    {
        return $this->__invoke($serviceLocator, CabotageOnlyFieldsetPopulator::class);
    }
}
