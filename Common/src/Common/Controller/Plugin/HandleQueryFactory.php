<?php

namespace Common\Controller\Plugin;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class HandleQueryFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): HandleQuery
    {
        $serviceLocator = $container->getServiceLocator();

        $annotationBuilder = $serviceLocator->get('TransferAnnotationBuilder');
        $queryService = $serviceLocator->get('QueryService');

        return new HandleQuery($annotationBuilder, $queryService);
    }

    /**
     * @deprecated
     */
    public function createService(ServiceLocatorInterface $serviceLocator): HandleQuery
    {
        return $this->__invoke($serviceLocator, HandleQuery::class);
    }
}
