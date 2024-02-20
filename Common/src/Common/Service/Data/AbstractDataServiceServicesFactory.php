<?php

namespace Common\Service\Data;

use Psr\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

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
}
