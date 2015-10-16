<?php

namespace Common\Service\Cqrs\Query;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CachingQueryServiceFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');
        $conf = isset($config['cacheable_queries']) ? $config['cacheable_queries'] : [];
        return new CachingQueryService($serviceLocator->get(QueryService::class), $serviceLocator->get('Cache'), $conf);
    }
}
