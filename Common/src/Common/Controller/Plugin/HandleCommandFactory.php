<?php

namespace Common\Controller\Plugin;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Handle Command Factory
 */
class HandleCommandFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): HandleCommand
    {
        $serviceLocator = $container->getServiceLocator();

        return new HandleCommand($serviceLocator->get('CommandSender'), $serviceLocator->get('Helper\FlashMessenger'));
    }

    /**
     * @deprecated
     */
    public function createService(ServiceLocatorInterface $serviceLocator): HandleCommand
    {
        return $this->__invoke($serviceLocator, HandleCommand::class);
    }
}
