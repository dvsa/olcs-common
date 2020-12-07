<?php

namespace Common\Service\Cqrs\Query;

use Common\Service\Cqrs\Exception\CacheTtlException;
use Common\Service\Cqrs\RecoverHttpClientExceptionTrait;
use Dvsa\Olcs\Transfer\Query\Cache\ById;
use Dvsa\Olcs\Transfer\Query\CacheableLongTermQueryInterface;
use Dvsa\Olcs\Transfer\Query\CacheableMediumTermQueryInterface;
use Dvsa\Olcs\Transfer\Query\QueryContainerInterface;
use Dvsa\Olcs\Transfer\Service\CacheEncryption as CacheEncryptionService;
use Dvsa\Olcs\Transfer\Util\Annotation\AnnotationBuilder;

/**
 * Class CachingQueryService
 * @package Common\Service\Cqrs\Query
 */
class CachingQueryService implements QueryServiceInterface, \Laminas\Log\LoggerAwareInterface
{
    use \Laminas\Log\LoggerAwareTrait;
    use RecoverHttpClientExceptionTrait;

    const BACKEND_FAIL_MSG = 'Backend DB failure HTTP code: %s';
    const CACHE_FAIL_MSG = 'Cache failure: %s';
    const CACHE_LOCAL_SAVE_MSG = 'Storing in local cache: %s';
    const CACHE_LOCAL_RETRIEVE_MSG = 'Fetching from local cache: %s';
    const CACHE_PERSISTENT_SAVE_MSG = 'Storing in persistent cache with TTL of %u seconds: %s';
    const CACHE_PERSISTENT_RETRIEVE_MSG = 'Fetching from persistent cache: %s';
    const CACHE_ENCRYPTION_MODE_MSG = 'Using encryption mode: %s';
    const MISSING_TTL_INTERFACE_TYPE = 'No TTL value found for this query';

    /** @var QueryServiceInterface */
    private $queryService;

    /** @var array */
    private $localCache;

    /** @var CacheEncryptionService */
    private $cacheService;

    /** @var AnnotationBuilder */
    private $annotationBuilder;

    /** @var bool */
    private $enabled;

    /** @var array */
    private $ttl;

    /**
     * Constructor
     *
     * @param QueryServiceInterface  $queryService Query service
     * @param CacheEncryptionService $cacheService Cache storage with automatic encryption built in
     * @param bool                   $enabled      Whether the cache is enabled
     * @param array                  $ttl          Ttl of the various cache types
     */
    public function __construct(
        QueryServiceInterface $queryService,
        CacheEncryptionService $cache,
        AnnotationBuilder $annotationBuilder,
        $enabled,
        array $ttl
    ) {
        $this->queryService = $queryService;
        $this->cacheService = $cache;
        $this->annotationBuilder = $annotationBuilder;
        $this->enabled = $enabled;
        $this->ttl = $ttl;
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

        if (!$this->enabled) {
            return $this->queryService->send($query);
        }

        if ($query->isPersistentCacheable()) {
            try {
                return $this->handlePersistentCache($query);
            } catch (\Exception $e) {
                //error has occurred with the cache - log the error and retrieve fresh from the backend
                $this->logError(sprintf(self::CACHE_FAIL_MSG, $e->getMessage()));
            }
        }

        if ($query->isShortTermCacheable()) {
            return $this->handleLocalCache($query);
        }

        return $this->queryService->send($query);
    }

    /**
     * Retrieve data that is not found in the usual CQRS cache
     *
     * @param string $identifier
     * @param string $uniqueId
     *
     * @return mixed|null
     * @throws \Exception
     */
    public function handleCustomCache(string $identifier, string $uniqueId = '')
    {
        try {
            if ($this->cacheService->hasCustomItem($identifier, $uniqueId)) {
                return $this->getCustomCache($identifier, $uniqueId);
            }
        } catch (\Exception $e) {
            //error has occurred with the cache - log the error and retrieve fresh from the backend
            $this->logError(sprintf(self::CACHE_FAIL_MSG, $e->getMessage()));
        }

        $queryParams = [
            'id' => $identifier,
            'uniqueId' => $uniqueId
        ];

        $dto = ById::create($queryParams);
        $query = $this->annotationBuilder->createQuery($dto);
        $response = $this->send($query);

        if ($response->isOk()) {
            return $response->getResult();
        }

        throw new \Exception(sprintf(self::BACKEND_FAIL_MSG, $response->getStatusCode()));
    }

    /**
     * Retrieve data that is not found in the usual CQRS cache
     *
     * @param string $identifier
     * @param string $uniqueId
     *
     * @return mixed|null
     * @throws \Exception
     */
    public function getCustomCache(string $identifier, string $uniqueId = '')
    {
        return $this->cacheService->getCustomItem($identifier, $uniqueId);
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

                try {
                    $ttl = $this->getCacheTtl($query);
                } catch (CacheTtlException $e) {
                    $this->logError(sprintf(self::CACHE_FAIL_MSG, $e->getMessage()));
                    return $result;
                }

                $this->logMessage(sprintf(self::CACHE_PERSISTENT_SAVE_MSG, $ttl, $dtoClassName));

                $this->cacheService->setItem($cacheIdentifier, $encryptionMode, $result, $ttl);
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
     * Get the cache ttl depending on the query type
     *
     * @param QueryContainerInterface $query
     *
     * @throws CacheTtlException
     * @return int
     */
    private function getCacheTtl(QueryContainerInterface $query): int
    {
        if ($query->isMediumTermCacheable() && isset($this->ttl[CacheableMediumTermQueryInterface::class])) {
            return $this->ttl[CacheableMediumTermQueryInterface::class];
        }

        if ($query->isLongTermCacheable() && isset($this->ttl[CacheableLongTermQueryInterface::class])) {
            return $this->ttl[CacheableLongTermQueryInterface::class];
        }

        throw new CacheTtlException(self::MISSING_TTL_INTERFACE_TYPE);
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
