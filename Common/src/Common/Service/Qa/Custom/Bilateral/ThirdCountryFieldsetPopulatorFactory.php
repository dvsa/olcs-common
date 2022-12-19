<?php

namespace Common\Service\Qa\Custom\Bilateral;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class ThirdCountryFieldsetPopulatorFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ThirdCountryFieldsetPopulator
    {
        return new ThirdCountryFieldsetPopulator(
            $container->get('Helper\Translation'),
            $container->get('QaBilateralYesNoWithMarkupForNoPopulator'),
            $container->get('QaBilateralStandardYesNoValueOptionsGenerator')
        );
    }

    /**
     * @deprecated
     */
    public function createService(ServiceLocatorInterface $serviceLocator): ThirdCountryFieldsetPopulator
    {
        return $this->__invoke($serviceLocator, ThirdCountryFieldsetPopulator::class);
    }
}
