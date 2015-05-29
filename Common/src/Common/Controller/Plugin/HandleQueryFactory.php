<?php

namespace Common\Controller\Plugin;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class HandleQueryFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $serviceLocator = $serviceLocator->getServiceLocator();
        
        $annotationBuilder = $serviceLocator->get('TransferAnnotationBuilder');
        $queryService = $serviceLocator->get('QueryService');

        return new HandleQuery($annotationBuilder, $queryService);
    }
}
