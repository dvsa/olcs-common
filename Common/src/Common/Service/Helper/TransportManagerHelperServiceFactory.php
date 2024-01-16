<?php

namespace Common\Service\Helper;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

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
}
