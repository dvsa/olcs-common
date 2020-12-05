<?php

namespace Common\Service\Cqrs\Query;

use Dvsa\Olcs\Transfer\Service\CacheEncryption as CacheEncryptionService;
use Exception;
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

        if (!isset($config['query_cache'])) {
            throw new Exception('Query cache config key missing');
        }

        if (!isset($config['query_cache']['enabled'])) {
            throw new Exception('Query cache enabled/disabled config key missing');
        }

        $service = new CachingQueryService(
            $serviceLocator->get(QueryService::class),
            $serviceLocator->get(CacheEncryptionService::class),
            $serviceLocator->get('TransferAnnotationBuilder'),
            $config['query_cache']['enabled'],
            $config['query_cache']['ttl']
        );

        $service->setLogger($serviceLocator->get('Logger'));

        return $service;
    }
}
