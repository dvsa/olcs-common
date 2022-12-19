<?php

namespace Common\Service\Cqrs\Query;

use Dvsa\Olcs\Transfer\Service\CacheEncryption as CacheEncryptionService;
use Exception;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Class CachingQueryServiceFactory
 * @package Common\Service\Cqrs\Query
 */
class CachingQueryServiceFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): CachingQueryService
    {
        $config = $container->get('Config');

        if (!isset($config['query_cache'])) {
            throw new Exception('Query cache config key missing');
        }

        if (!isset($config['query_cache']['enabled'])) {
            throw new Exception('Query cache enabled/disabled config key missing');
        }

        $service = new CachingQueryService(
            $container->get(QueryService::class),
            $container->get(CacheEncryptionService::class),
            $container->get('TransferAnnotationBuilder'),
            $config['query_cache']['enabled'],
            $config['query_cache']['ttl']
        );

        $service->setLogger($container->get('Logger'));

        return $service;
    }

    /**
     * @deprecated
     */
    public function createService(ServiceLocatorInterface $serviceLocator): CachingQueryService
    {
        return $this->__invoke($serviceLocator, CachingQueryService::class);
    }
}
