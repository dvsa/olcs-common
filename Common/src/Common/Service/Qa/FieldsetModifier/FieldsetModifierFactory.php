<?php

namespace Common\Service\Qa\FieldsetModifier;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

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
