<?php

namespace Common\Service\Qa;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class RadioFieldsetPopulatorFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): RadioFieldsetPopulator
    {
        return new RadioFieldsetPopulator(
            $container->get('QaRadioFactory'),
            $container->get('QaTranslateableTextHandler')
        );
    }

    /**
     * @deprecated
     */
    public function createService(ServiceLocatorInterface $serviceLocator): RadioFieldsetPopulator
    {
        return $this->__invoke($serviceLocator, RadioFieldsetPopulator::class);
    }
}
