<?php

namespace Common\Service\Translator;

use Common\Service\Cqrs\Query\CachingQueryService;
use Dvsa\Olcs\Transfer\Query\TranslationCache\Key;
use Dvsa\Olcs\Transfer\Service\CacheEncryption;
use Dvsa\Olcs\Transfer\Util\Annotation\AnnotationBuilder;
use Olcs\Logging\Log\Logger;
use Zend\I18n\Translator\Loader\PhpMemoryArray;
use Zend\I18n\Translator\Loader\RemoteLoaderInterface;
use Zend\I18n\Translator\TextDomain;

/**
 * Loads translations from the Redis cache, if cache unavailable this will fallback to the database
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class TranslationLoader implements RemoteLoaderInterface
{
    const ERR_CACHE = 'Translation cache failure: %s';
    const ERR_UNABLE_TO_LOAD = 'Translations could not be loaded';

    /** @var CachingQueryService $queryService */
    private $queryService;

    /** @var AnnotationBuilder $annotationBuilder */
    private $annotationBuilder;

    /**
     * TranslationLoader constructor.
     *
     * @param CachingQueryService $queryService
     * @param AnnotationBuilder   $annotationBuilder
     *
     * @return void
     */
    public function __construct(CachingQueryService $queryService, AnnotationBuilder $annotationBuilder)
    {
        $this->queryService = $queryService;
        $this->annotationBuilder = $annotationBuilder;
    }

    /**
     * Load translation information based on the locale
     *
     * @param string $locale
     * @param string $textDomain needed to comply with interface but not needed by us
     *
     * @return TextDomain
     * @throws \Exception
     */
    public function load($locale, $textDomain)
    {
        try {
            $messages = $this->queryService->handleCustomCache(CacheEncryption::TRANSLATION_KEY_IDENTIFIER, $locale);
        } catch (\Exception $e) {
            $messages = false;
            $errorMessage = sprintf(self::ERR_CACHE, $e->getMessage());
            Logger::err($errorMessage);
        }

        /**
         * If Redis cache was empty, or an exception was thrown, try to retrieve translations from the database
         */
        if (!$messages) {
            $query = Key::create(['id' => $locale]);

            $response = $this->queryService->send(
                $this->annotationBuilder->createQuery($query)
            );

            if (!$response->isOk()) {
                throw new \Exception(self::ERR_UNABLE_TO_LOAD);
            }

            $messages = $response->getResult();
        }

        $zendMemoryArray = new PhpMemoryArray($messages);

        return $zendMemoryArray->load($locale, $textDomain);
    }
}
