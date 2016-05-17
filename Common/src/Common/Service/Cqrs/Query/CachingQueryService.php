<?php

namespace Common\Service\Cqrs\Query;

use Dvsa\Olcs\Transfer\Query\QueryContainerInterface;
use Zend\Cache\Storage\StorageInterface as CacheInterface;

/**
 * Class CachingQueryService
 * @package Common\Service\Cqrs\Query
 */
class CachingQueryService implements QueryServiceInterface, \Zend\Log\LoggerAwareInterface
{
    use \Zend\Log\LoggerAwareTrait;

    /**
     * @var QueryServiceInterface
     */
    private $queryService;

    /**
     * @var
     */
    private $localCache;

    /**
     * @var CacheInterface
     */
    private $cacheService;

    /**
     * @param QueryServiceInterface $queryService
     * @param CacheInterface $cache
     */
    public function __construct(QueryServiceInterface $queryService, CacheInterface $cache)
    {
        $this->queryService = $queryService;
        $this->cacheService = $cache;
    }

    /**
     * @param QueryContainerInterface $query
     * @return \Common\Service\Cqrs\Response
     */
    public function send(QueryContainerInterface $query)
    {
        if ($query->isMediumTermCachable()) {
            return $this->handlePersistentCache($query);
        }

        if ($query->isShortTermCachable()) {
            return $this->handleLocalCache($query);
        }

        return $this->queryService->send($query);
    }

    /**
     * @param QueryContainerInterface $query
     * @return mixed
     */
    private function handleLocalCache(QueryContainerInterface $query)
    {
        $cacheIdentifier = $query->getCacheIdentifier();

        if (!isset($this->localCache[$cacheIdentifier])) {
            $this->localCache[$cacheIdentifier] = $this->queryService->send($query);
        } else {
            $this->logMessage('Get from local cache '. get_class($query->getDto()));
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
            $this->logMessage('Get from presistent cache '. get_class($query->getDto()));
            $result = $this->cacheService->getItem($query->getCacheIdentifier());
        }

        return $result;
    }

    /**
     * Log a message to the injected logger
     *
     * @param string $message
     */
    private function logMessage($message)
    {
        if ($this->getLogger()) {
            $this->getLogger()->debug($message);
        }
    }
}
