<?php

namespace Common\Service\Qa\Custom\Ecmt;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class RestrictedCountriesFieldsetPopulatorFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): RestrictedCountriesFieldsetPopulator
    {
        return new RestrictedCountriesFieldsetPopulator(
            $container->get('QaEcmtYesNoRadioFactory'),
            $container->get('QaEcmtRestrictedCountriesMultiCheckboxFactory')
        );
    }

    /**
     * @deprecated
     */
    public function createService(ServiceLocatorInterface $serviceLocator): RestrictedCountriesFieldsetPopulator
    {
        return $this->__invoke($serviceLocator, RestrictedCountriesFieldsetPopulator::class);
    }
}
