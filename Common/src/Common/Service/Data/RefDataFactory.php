<?php

namespace Common\Service\Data;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * RefDataFactory
 */
class RefDataFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     *
     * @return RefData
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): RefData
    {
        return new RefData(
            $container->get(RefDataServices::class)
        );
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $services
     *
     * @return RefData
     */
    public function createService(ServiceLocatorInterface $services): RefData
    {
        return $this($services, RefData::class);
    }
}
