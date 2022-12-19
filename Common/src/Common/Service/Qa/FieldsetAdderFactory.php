<?php

namespace Common\Service\Qa;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class FieldsetAdderFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): FieldsetAdder
    {
        return new FieldsetAdder(
            $container->get('QaFieldsetPopulatorProvider'),
            $container->get('QaFieldsetFactory'),
            $container->get('QaFieldsetModifier')
        );
    }

    /**
     * @deprecated
     */
    public function createService(ServiceLocatorInterface $serviceLocator): FieldsetAdder
    {
        return $this->__invoke($serviceLocator, FieldsetAdder::class);
    }
}
