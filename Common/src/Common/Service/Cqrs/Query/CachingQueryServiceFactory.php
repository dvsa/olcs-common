<?php

namespace Common\Service\Cqrs\Query;

use Dvsa\Olcs\Transfer\Service\CacheEncryption as CacheEncryptionService;
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
        $service = new CachingQueryService(
            $serviceLocator->get(QueryService::class),
            $serviceLocator->get(CacheEncryptionService::class)
        );

        $service->setLogger($serviceLocator->get('Logger'));

        return $service;
    }
}
