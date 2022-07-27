<?php

namespace Common\View\Helper;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class ApplicationNameFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     *
     * @return ApplicationName
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ApplicationName
    {
        return new ApplicationName(
            $container->getServiceLocator()->get('Config')
        );
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $services
     *
     * @return ApplicationName
     */
    public function createService(ServiceLocatorInterface $services)
    {
        return $this($services, ApplicationName::class);
    }
}
