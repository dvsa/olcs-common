<?php

namespace Common\Service\Data;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * LicenceOperatingCentreFactory
 */
class LicenceOperatingCentreFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     *
     * @return LicenceOperatingCentre
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): LicenceOperatingCentre
    {
        return new LicenceOperatingCentre(
            $container->get('DataServiceManager')->get(AbstractDataServiceServices::class),
            $container->get('DataServiceManager')->get(Licence::class)
        );
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $services
     *
     * @return LicenceOperatingCentre
     */
    public function createService(ServiceLocatorInterface $services): LicenceOperatingCentre
    {
        return $this($services, LicenceOperatingCentre::class);
    }
}
