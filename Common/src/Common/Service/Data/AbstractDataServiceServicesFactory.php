<?php

namespace Common\Service\Data;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * AbstractDataServiceServicesFactory
 */
class AbstractDataServiceServicesFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     *
     * @return AbstractDataServiceServices
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): AbstractDataServiceServices
    {
        return new AbstractDataServiceServices(
            $container->get('TransferAnnotationBuilder'),
            $container->get('QueryService'),
            $container->get('CommandService')
        );
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $services
     *
     * @return AbstractDataServiceServices
     */
    public function createService(ServiceLocatorInterface $services): AbstractDataServiceServices
    {
        return $this($services, AbstractDataServiceServices::class);
    }
}
