<?php

namespace Common\Service\Data;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * AbstractListDataServiceServicesFactory
 */
class AbstractListDataServiceServicesFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     *
     * @return AbstractListDataServiceServices
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): AbstractListDataServiceServices
    {
        return new AbstractListDataServiceServices(
            $container->get(AbstractDataServiceServices::class)
        );
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $services
     *
     * @return AbstractListDataServiceServices
     */
    public function createService(ServiceLocatorInterface $services): AbstractListDataServiceServices
    {
        return $this($services, AbstractListDataServiceServices::class);
    }
}
