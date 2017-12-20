<?php

namespace Common\Service\Cqrs\Query;

use Common\Service\Cqrs\RecoverHttpClientExceptionTrait;
use Dvsa\Olcs\Transfer\Query\QueryContainerInterface;
use Zend\Cache\Storage\StorageInterface as CacheInterface;

/**
 * Class CachingQueryService
 * @package Common\Service\Cqrs\Query
 */
class CachingQueryService implements QueryServiceInterface, \Zend\Log\LoggerAwareInterface
{
    use \Zend\Log\LoggerAwareTrait;
    use RecoverHttpClientExceptionTrait;

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
     * Constructor
     *
     * @param QueryServiceInterface $queryService Query service
     * @param CacheInterface        $cache        Cache storage
     */
    public function __construct(QueryServiceInterface $queryService, CacheInterface $cache)
    {
        $this->queryService = $queryService;
        $this->cacheService = $cache;
    }

    /**
     * Send a query to the backend
     *
     * @param QueryContainerInterface $query Query container
     *
     * @return \Common\Service\Cqrs\Response
     */
    public function send(QueryContainerInterface $query)
    {
        $this->queryService->setRecoverHttpClientException($this->getRecoverHttpClientException());
        if ($query->isMediumTermCachable()) {
            return $this->handlePersistentCache($query);
        }

        if ($query->isShortTermCachable()) {
            return $this->handleLocalCache($query);
        }

        return $this->queryService->send($query);
    }

    /**
     * Handle a query using a local cache, cache is a class property, therefore cache is valid only for current request
     *
     * @param QueryContainerInterface $query Query continer
     *
     * @return \Common\Service\Cqrs\Response
     */
    private function handleLocalCache(QueryContainerInterface $query)
    {
        $cacheIdentifier = $query->getCacheIdentifier();

        if (isset($this->localCache[$cacheIdentifier])) {
            $this->logMessage('Get from local cache ' . get_class($query->getDto()));

            return $this->localCache[$cacheIdentifier];
        }

        $result = $this->queryService->send($query);
        if ($result->isOk()) {
            $this->localCache[$cacheIdentifier] = $result;
        }

        return $result;
    }

    /**
     * Handle a query using cache storage, lifetime of cache is from settings
     *
     * @param QueryContainerInterface $query Query continer
     *
     * @return \Common\Service\Cqrs\Response
     */
    private function handlePersistentCache(QueryContainerInterface $query)
    {
        $cacheIdentifier = $query->getCacheIdentifier();
        $success = $this->cacheService->hasItem($cacheIdentifier);

        if (!$success) {
            $result = $this->queryService->send($query);
            if ($result->isOk()) {
                $this->cacheService->setItem($cacheIdentifier, $result);
            }
        } else {
            $this->logMessage('Get from presistent cache '. get_class($query->getDto()));
            $result = $this->cacheService->getItem($cacheIdentifier);
        }

        return $result;
    }

    /**
     * Log a message to the injected logger
     *
     * @param string $message Message to log
     *
     * @return void
     */
    private function logMessage($message)
    {
        if ($this->getLogger()) {
            $this->getLogger()->debug($message);
        }
    }
}
