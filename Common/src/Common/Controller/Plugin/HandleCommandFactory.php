<?php

namespace Common\Controller\Plugin;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

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

        return new HandleCommand($serviceLocator->get('CommandSender'), $serviceLocator->get('Helper\FlashMessenger'));
    }
}
