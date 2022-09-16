<?php

namespace Common\Service\Helper;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * TransportManagerHelperServiceFactory
 */
class TransportManagerHelperServiceFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     *
     * @return TransportManagerHelperService
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): TransportManagerHelperService
    {
        return new TransportManagerHelperService(
            $container->get('TransferAnnotationBuilder'),
            $container->get('QueryService'),
            $container->get('Helper\Form'),
            $container->get('Helper\Date'),
            $container->get('Helper\Translation'),
            $container->get('Helper\Url'),
            $container->get('Table')
        );
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $services
     *
     * @return TransportManagerHelperService
     */
    public function createService(ServiceLocatorInterface $services): TransportManagerHelperService
    {
        return $this($services, TransportManagerHelperService::class);
    }
}
