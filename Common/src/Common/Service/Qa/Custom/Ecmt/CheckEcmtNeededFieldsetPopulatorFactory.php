<?php

namespace Common\Service\Qa\Custom\Ecmt;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class CheckEcmtNeededFieldsetPopulatorFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): CheckEcmtNeededFieldsetPopulator
    {
        return new CheckEcmtNeededFieldsetPopulator(
            $container->get('QaCheckboxFieldsetPopulator'),
            $container->get('QaEcmtInfoIconAdder')
        );
    }

    /**
     * @deprecated
     */
    public function createService(ServiceLocatorInterface $serviceLocator): CheckEcmtNeededFieldsetPopulator
    {
        return $this->__invoke($serviceLocator, CheckEcmtNeededFieldsetPopulator::class);
    }
}
