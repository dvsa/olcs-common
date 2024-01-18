<?php

namespace Common\Service\Helper;

use Common\Service\AntiVirus\Scan;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

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
}
