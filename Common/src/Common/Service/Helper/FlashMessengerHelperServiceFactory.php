<?php

namespace Common\Service\Helper;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * FlashMessengerHelperServiceFactory
 */
class FlashMessengerHelperServiceFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     *
     * @return FlashMessengerHelperService
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): FlashMessengerHelperService
    {
        return new FlashMessengerHelperService(
            $container->get('ControllerPluginManager')->get('FlashMessenger')
        );
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $services
     *
     * @return FlashMessengerHelperService
     */
    public function createService(ServiceLocatorInterface $services): FlashMessengerHelperService
    {
        return $this($services, FlashMessengerHelperService::class);
    }
}
