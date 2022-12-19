<?php

namespace Common\Service\Qa;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class FieldsetPopulatorFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): FieldsetPopulator
    {
        return new FieldsetPopulator(
            $container->get('QaFieldsetAdder'),
            $container->get('QaValidatorsAdder')
        );
    }

    /**
     * @deprecated
     */
    public function createService(ServiceLocatorInterface $serviceLocator): FieldsetPopulator
    {
        return $this->__invoke($serviceLocator, FieldsetPopulator::class);
    }
}
