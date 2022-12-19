<?php

namespace Common\Service\Qa\Custom\Ecmt;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class AnnualTripsAbroadFieldsetPopulatorFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): AnnualTripsAbroadFieldsetPopulator
    {
        return new AnnualTripsAbroadFieldsetPopulator(
            $container->get('QaTextFieldsetPopulator'),
            $container->get('Helper\Translation'),
            $container->get('QaEcmtNiWarningConditionalAdder'),
            $container->get('QaCommonHtmlAdder')
        );
    }

    /**
     * @deprecated
     */
    public function createService(ServiceLocatorInterface $serviceLocator): AnnualTripsAbroadFieldsetPopulator
    {
        return $this->__invoke($serviceLocator, AnnualTripsAbroadFieldsetPopulator::class);
    }
}
