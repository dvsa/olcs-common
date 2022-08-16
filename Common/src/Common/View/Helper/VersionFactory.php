<?php

namespace Common\View\Helper;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class VersionFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     *
     * @return Version
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): Version
    {
        return new Version(
            $container->getServiceLocator()->get('Config')
        );
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $services
     *
     * @return Version
     */
    public function createService(ServiceLocatorInterface $services)
    {
        return $this($services, Version::class);
    }
}
