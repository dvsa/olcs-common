<?php

namespace Common\Controller\Plugin;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class HandleQueryFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): HandleQuery
    {
        if (method_exists($container, 'getServiceLocator') && $container->getServiceLocator()) {
            $container = $container->getServiceLocator();
        }

        $annotationBuilder = $container->get('TransferAnnotationBuilder');
        $queryService = $container->get('QueryService');

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
