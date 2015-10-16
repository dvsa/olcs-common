<?php

namespace Common\Service\Cqrs\Query;

use Dvsa\Olcs\Transfer\Query\QueryContainerInterface;
use Zend\Cache\Storage\StorageInterface as CacheInterface;

class CachingQueryService implements QueryServiceInterface
{
    private $queryService;

    private $config = [];

    private $localCache;

    private $cacheService;

    /**
     * @param QueryServiceInterface $queryService
     * @param CacheInterface $cache
     * @param array $config
     */
    public function __construct(QueryServiceInterface $queryService, CacheInterface $cache, array $config)
    {
        $this->queryService = $queryService;
        $this->cacheService = $cache;
        $this->config = $config;
    }

    /**
     * @param QueryContainerInterface $query
     * @return \Common\Service\Cqrs\Response
     */
    public function send(QueryContainerInterface $query)
    {
        if (!$query->isCachable()) {
            return $this->queryService->send($query);
        }

        $queryClass = get_class($query->getDto());

        if (isset($this->config[$queryClass]['persistent']) && $this->config[$queryClass]['persistent'] === true) {
            return $this->handlePersistentCache($query);
        }

        return $this->handleLocalCache($query);
    }

    private function handleLocalCache(QueryContainerInterface $query)
    {
        $cacheIdentifier = $query->getCacheIdentifier();

        if (!isset($this->localCache[$cacheIdentifier])) {
            $this->localCache[$cacheIdentifier] = $this->queryService->send($query);
        }

        return $this->localCache[$cacheIdentifier];
    }

    /**
     * @param QueryContainerInterface $query
     * @return \Common\Service\Cqrs\Response
     */
    private function handlePersistentCache(QueryContainerInterface $query)
    {
        $success = $this->cacheService->hasItem($query->getCacheIdentifier());

        if (!$success) {
            $result = $this->queryService->send($query);
            $this->cacheService->setItem($query->getCacheIdentifier(), $result);
        } else {
            $result = $this->cacheService->getItem($query->getCacheIdentifier());
        }

        return $result;
    }
}
