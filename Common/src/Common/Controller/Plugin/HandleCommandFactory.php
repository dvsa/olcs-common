<?php

namespace Common\Controller\Plugin;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Handle Command Factory
 */
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
        $serviceLocator = $serviceLocator->getServiceLocator();

        $annotationBuilder = $serviceLocator->get('TransferAnnotationBuilder');
        $commandService = $serviceLocator->get('CommandService');
        $fm = $serviceLocator->get('Helper\FlashMessenger');

        return new HandleCommand($annotationBuilder, $commandService, $fm);
    }
}
