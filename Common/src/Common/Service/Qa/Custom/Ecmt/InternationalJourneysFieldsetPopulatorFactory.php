<?php

namespace Common\Service\Qa\Custom\Ecmt;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class InternationalJourneysFieldsetPopulatorFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): InternationalJourneysFieldsetPopulator
    {
        return new InternationalJourneysFieldsetPopulator(
            $container->get('QaRadioFieldsetPopulator'),
            $container->get('QaEcmtNiWarningConditionalAdder')
        );
    }

    /**
     * @deprecated
     */
    public function createService(ServiceLocatorInterface $serviceLocator): InternationalJourneysFieldsetPopulator
    {
        return $this->__invoke($serviceLocator, InternationalJourneysFieldsetPopulator::class);
    }
}
