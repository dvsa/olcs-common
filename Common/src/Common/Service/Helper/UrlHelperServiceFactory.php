<?php

namespace Common\Service\Helper;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * UrlHelperServiceFactory
 */
class UrlHelperServiceFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     *
     * @return UrlHelperService
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): UrlHelperService
    {
        return new UrlHelperService(
            $container->get('ViewHelperManager'),
            $container->get('config')
        );
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $services
     *
     * @return UrlHelperService
     */
    public function createService(ServiceLocatorInterface $services): UrlHelperService
    {
        return $this($services, UrlHelperService::class);
    }
}
