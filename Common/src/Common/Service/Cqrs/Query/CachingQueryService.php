<?php

namespace Common\Service\Cqrs\Query;

use Common\Service\Cqrs\RecoverHttpClientExceptionTrait;
use Dvsa\Olcs\Transfer\Query\QueryContainerInterface;
use Dvsa\Olcs\Transfer\Service\CacheEncryption as CacheEncryptionService;

/**
 * Class CachingQueryService
 * @package Common\Service\Cqrs\Query
 */
class CachingQueryService implements QueryServiceInterface, \Zend\Log\LoggerAwareInterface
{
    use \Zend\Log\LoggerAwareTrait;
    use RecoverHttpClientExceptionTrait;

    const CACHE_FAIL_MSG = 'Cache failure: %s';
    const CACHE_LOCAL_SAVE_MSG = 'Storing in local cache: %s';
    const CACHE_LOCAL_RETRIEVE_MSG = 'Fetching from local cache: %s';
    const CACHE_PERSISTENT_SAVE_MSG = 'Storing in persistent cache: %s';
    const CACHE_PERSISTENT_RETRIEVE_MSG = 'Fetching from persistent cache: %s';
    const CACHE_ENCRYPTION_MODE_MSG = 'Using encryption mode: %s';

    /**
     * @var QueryServiceInterface
     */
    private $queryService;

    /**
     * @var array
     */
    private $localCache;

    /**
     * @var CacheEncryptionService
     */
    private $cacheService;

    /**
     * Constructor
     *
     * @param QueryServiceInterface  $queryService Query service
     * @param CacheEncryptionService $cacheService Cache storage with automatic encryption built in
     */
    public function __construct(QueryServiceInterface $queryService, CacheEncryptionService $cache)
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
            try {
                return $this->handlePersistentCache($query);
            } catch (\Exception $e) {
                //error has occurred with the cache - log the error and retrieve fresh from the backend
                $this->logError(sprintf(self::CACHE_FAIL_MSG, $e->getMessage()));
            }
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
        $dtoClassName = $query->getDtoClassName();

        if ($this->localCacheHasItem($cacheIdentifier)) {
            return $this->retrieveLocalCache($cacheIdentifier, $dtoClassName);
        }

        $result = $this->queryService->send($query);
        if ($result->isOk()) {
            $this->storeLocalCache($cacheIdentifier, $dtoClassName, $result);
        }

        return $result;
    }

    /**
     * Check if the local cache has the item
     *
     * @param string $cacheIdentifier
     *
     * @return bool
     */
    private function localCacheHasItem(string $cacheIdentifier): bool
    {
        return isset($this->localCache[$cacheIdentifier]);
    }

    /**
     * Retrieve a record from the local cache
     *
     * @param string $cacheIdentifier
     * @param string $dtoClassName
     *
     * @return mixed
     */
    private function retrieveLocalCache(string $cacheIdentifier, string $dtoClassName)
    {
        $this->logMessage(sprintf(self::CACHE_LOCAL_RETRIEVE_MSG, $dtoClassName));
        return $this->localCache[$cacheIdentifier];
    }

    /**
     * Retrieve a record from the local cache
     *
     * @param string $cacheIdentifier
     * @param string $dtoClassName
     * @param mixed  $result
     *
     * @return void
     */
    private function storeLocalCache(string $cacheIdentifier, string $dtoClassName, $result): void
    {
        $this->logMessage(sprintf(self::CACHE_LOCAL_SAVE_MSG, $dtoClassName));
        $this->localCache[$cacheIdentifier] = $result;
    }

    /**
     * Handle a query using cache storage, lifetime of cache is from settings
     *
     * @param QueryContainerInterface $query Query container
     *
     * @return \Common\Service\Cqrs\Response
     */
    private function handlePersistentCache(QueryContainerInterface $query)
    {
        $cacheIdentifier = $query->getCacheIdentifier();
        $dtoClassName = $query->getDtoClassName();

        //check the local cache first
        if ($this->localCacheHasItem($cacheIdentifier)) {
            return $this->retrieveLocalCache($cacheIdentifier, $dtoClassName);
        }

        $encryptionMode = $query->getEncryptionMode();
        $this->logMessage(sprintf(self::CACHE_ENCRYPTION_MODE_MSG, $encryptionMode));

        /**
         * see if the cache has the item
         * additionally checks if the information is available to the node where the cache is running
         */
        $success = $this->cacheService->hasItem($cacheIdentifier, $encryptionMode);

        if (!$success) {
            $result = $this->queryService->send($query);
            if ($result->isOk()) {
                //add the result to the local cache to avoid future trips on the same request
                $this->storeLocalCache($cacheIdentifier, $dtoClassName, $result);
                $this->logMessage(sprintf(self::CACHE_PERSISTENT_SAVE_MSG, $dtoClassName));

                $this->cacheService->setItem($cacheIdentifier, $encryptionMode, $result);
            }
        } else {
            $this->logMessage(sprintf(self::CACHE_PERSISTENT_RETRIEVE_MSG, $dtoClassName));
            $result = $this->cacheService->getItem($cacheIdentifier, $encryptionMode);

            //add the result to the local cache to avoid future trips on the same request
            $this->storeLocalCache($cacheIdentifier, $dtoClassName, $result);
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

    /**
     * Log error to the injected logger
     *
     * @param string $error Error to log
     *
     * @return void
     */
    private function logError(string $error): void
    {
        if ($this->getLogger()) {
            $this->getLogger()->err($error);
        }
    }
}
