<?php

namespace Common\Service\Qa\FieldsetModifier;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class FieldsetModifierFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return FieldsetModifier
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $fieldsetModifier = new FieldsetModifier();

        $fieldsetModifier->registerModifier(
            $serviceLocator->get('QaRoadWorthinessMakeAndModelFieldsetModifier')
        );

        return $fieldsetModifier;
    }
}
