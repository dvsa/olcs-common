<?php

namespace Common\Service\Qa;

use Psr\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

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
}
