<?php

namespace Common\Service\Cqrs\Query;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class CachingQueryServiceFactory
 * @package Common\Service\Cqrs\Query
 */
class CachingQueryServiceFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return CachingQueryService
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');
        $conf = isset($config['cacheable_queries']) ? $config['cacheable_queries'] : [];
        return new CachingQueryService($serviceLocator->get(QueryService::class), $serviceLocator->get('Cache'), $conf);
    }
}
