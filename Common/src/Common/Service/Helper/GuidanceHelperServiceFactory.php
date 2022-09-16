<?php

namespace Common\Service\Helper;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * GuidanceHelperServiceFactory
 */
class GuidanceHelperServiceFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     *
     * @return GuidanceHelperService
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): GuidanceHelperService
    {
        return new GuidanceHelperService(
            $container->get('ViewHelperManager')->get('placeholder')
        );
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $services
     *
     * @return GuidanceHelperService
     */
    public function createService(ServiceLocatorInterface $services): GuidanceHelperService
    {
        return $this($services, GuidanceHelperService::class);
    }
}
