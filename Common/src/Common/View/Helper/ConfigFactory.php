<?php

namespace Common\View\Helper;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class ConfigFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     *
     * @return Config
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): Config
    {
        return new Config(
            $container->getServiceLocator()->get('Config')
        );
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $services
     *
     * @return Config
     */
    public function createService(ServiceLocatorInterface $services)
    {
        return $this($services, Config::class);
    }
}
