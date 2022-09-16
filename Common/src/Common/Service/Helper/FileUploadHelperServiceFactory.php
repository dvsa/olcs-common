<?php

namespace Common\Service\Helper;

use Common\Service\AntiVirus\Scan;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * FileUploadHelperServiceFactory
 */
class FileUploadHelperServiceFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     *
     * @return FileUploadHelperService
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): FileUploadHelperService
    {
        return new FileUploadHelperService(
            $container->get('Helper\Url'),
            $container->get(Scan::class)
        );
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $services
     *
     * @return FileUploadHelperService
     */
    public function createService(ServiceLocatorInterface $services): FileUploadHelperService
    {
        return $this($services, FileUploadHelperService::class);
    }
}
