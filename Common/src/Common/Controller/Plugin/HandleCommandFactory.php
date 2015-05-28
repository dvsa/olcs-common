<?php

namespace Common\Controller\Plugin;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class HandleCommandFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $annotationBuilder = $serviceLocator->get('TransferAnnotationBuilder');
        $commandService = $serviceLocator->get('CommandService');

        return new HandleCommand($annotationBuilder, $commandService);
    }
}
