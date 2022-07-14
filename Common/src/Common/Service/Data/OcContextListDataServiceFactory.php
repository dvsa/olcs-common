<?php

namespace Common\Service\Data;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class OcContextListDataServiceFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return OcContextListDataService
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): OcContextListDataService
    {
        return new OcContextListDataService(
            $container->get('DataServiceManager')->get('Common\Service\Data\LicenceOperatingCentre'),
            $container->get('DataServiceManager')->get('Common\Service\Data\ApplicationOperatingCentre'),
        );
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $services
     *
     * @return OcContextListDataService
     */
    public function createService(ServiceLocatorInterface $services)
    {
        return $this($services, OcContextListDataService::class);
    }
}
