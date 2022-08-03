<?php

namespace Common\Service\Data;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * ApplicationOperatingCentreFactory
 */
class ApplicationOperatingCentreFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     *
     * @return ApplicationOperatingCentre
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ApplicationOperatingCentre
    {
        return new ApplicationOperatingCentre(
            $container->get(AbstractDataServiceServices::class),
            $container->get(Application::class)
        );
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $services
     *
     * @return ApplicationOperatingCentre
     */
    public function createService(ServiceLocatorInterface $services): ApplicationOperatingCentre
    {
        return $this($services, ApplicationOperatingCentre::class);
    }
}
