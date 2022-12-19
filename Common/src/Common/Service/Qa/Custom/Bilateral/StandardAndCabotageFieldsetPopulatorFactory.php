<?php

namespace Common\Service\Qa\Custom\Bilateral;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class StandardAndCabotageFieldsetPopulatorFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): StandardAndCabotageFieldsetPopulator
    {
        return new StandardAndCabotageFieldsetPopulator(
            $container->get('QaBilateralRadioFactory'),
            $container->get('QaBilateralStandardAndCabotageYesNoRadioFactory'),
            $container->get('QaBilateralYesNoRadioOptionsApplier'),
            $container->get('QaBilateralStandardYesNoValueOptionsGenerator')
        );
    }

    /**
     * @deprecated
     */
    public function createService(ServiceLocatorInterface $serviceLocator): StandardAndCabotageFieldsetPopulator
    {
        return $this->__invoke($serviceLocator, StandardAndCabotageFieldsetPopulator::class);
    }
}
