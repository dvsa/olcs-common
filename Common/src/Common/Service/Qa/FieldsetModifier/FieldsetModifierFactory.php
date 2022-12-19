<?php

namespace Common\Service\Qa\FieldsetModifier;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class FieldsetModifierFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): FieldsetModifier
    {
        $fieldsetModifier = new FieldsetModifier();

        $fieldsetModifier->registerModifier(
            $container->get('QaRoadWorthinessMakeAndModelFieldsetModifier')
        );

        return $fieldsetModifier;
    }

    /**
     * @deprecated
     */
    public function createService(ServiceLocatorInterface $serviceLocator): FieldsetModifier
    {
        return $this->__invoke($serviceLocator, FieldsetModifier::class);
    }
}
