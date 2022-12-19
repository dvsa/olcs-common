<?php

namespace Common\Service\Qa\Custom\Bilateral;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class EmissionsStandardsFieldsetPopulatorFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): EmissionsStandardsFieldsetPopulator
    {
        return new EmissionsStandardsFieldsetPopulator(
            $container->get('QaCommonWarningAdder'),
            $container->get('Helper\Translation'),
            $container->get('QaBilateralYesNoWithMarkupForNoPopulator'),
            $container->get('QaBilateralYesNoValueOptionsGenerator')
        );
    }

    /**
     * @deprecated
     */
    public function createService(ServiceLocatorInterface $serviceLocator): EmissionsStandardsFieldsetPopulator
    {
        return $this->__invoke($serviceLocator, EmissionsStandardsFieldsetPopulator::class);
    }
}
