<?php

namespace Common\Service\Data;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

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
            $container->get('DataServiceManager')->get(LicenceOperatingCentre::class),
            $container->get('DataServiceManager')->get(ApplicationOperatingCentre::class)
        );
    }
}
