<?php

namespace Common\Service\Translator;

use Common\Service\Cqrs\Query\CachingQueryService;
use Dvsa\Olcs\Transfer\Service\CacheEncryption;
use Olcs\Logging\Log\Logger;
use Laminas\I18n\Translator\Loader\PhpMemoryArray;
use Laminas\I18n\Translator\Loader\RemoteLoaderInterface;
use Laminas\I18n\Translator\TextDomain;

/**
 * Loads translations from the Redis cache, if cache unavailable this will fallback to the database
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class TranslationLoader implements RemoteLoaderInterface
{
    const ERR_UNABLE_TO_LOAD_REPLACEMENTS = 'Replacements could not be loaded: ';
    const ERR_UNABLE_TO_LOAD = 'Translations could not be loaded: ';

    /** @var CachingQueryService $queryService */
    private $queryService;

    /**
     * TranslationLoader constructor.
     *
     * @param CachingQueryService $queryService
     *
     * @return void
     */
    public function __construct(CachingQueryService $queryService)
    {
        $this->queryService = $queryService;
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
            $message = sprintf(self::ERR_UNABLE_TO_LOAD, $e->getMessage());
            throw new \Exception($message);
        }

        $phpMemoryArray = new PhpMemoryArray($messages);
        return $phpMemoryArray->load($locale, $textDomain);
    }

    /**
     * Load translation replacements
     *
     * @return array
     * @throws \Exception
     */
    public function loadReplacements(): array
    {
        try {
            $replacements = $this->queryService->handleCustomCache(
                CacheEncryption::TRANSLATION_REPLACEMENT_IDENTIFIER
            );
        } catch (\Exception $e) {
            $replacements = [];
            $errorMessage = sprintf(self::ERR_UNABLE_TO_LOAD_REPLACEMENTS, $e->getMessage());
            Logger::err($errorMessage);
        }

        return $replacements;
    }
}
