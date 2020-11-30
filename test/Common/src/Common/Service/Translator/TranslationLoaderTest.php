<?php

namespace CommonTest\Service\Translator;

use Common\Service\Cqrs\Query\CachingQueryService;
use Common\Service\Translator\TranslationLoader;
use Dvsa\Olcs\Transfer\Service\CacheEncryption;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\I18n\Translator\TextDomain;

/**
 * TranslationLoaderTest
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class TranslationLoaderTest extends MockeryTestCase
{
    /**
     * test loading translations from the cache
     */
    public function testLoadTranslationsFromCache()
    {
        $locale = 'en_GB';
        $textDomain = 'default';
        $actualMessages = ['some_key' => 'some_text'];
        $cacheIdentifier = CacheEncryption::TRANSLATION_KEY_IDENTIFIER;

        $messages = [
            $textDomain => [
                $locale => $actualMessages,
            ],
        ];

        $mockCache = m::mock(CachingQueryService::class);
        $mockCache->expects('handleCustomCache')
            ->with($cacheIdentifier, $locale)
            ->andReturn($messages);

        $loader = new TranslationLoader($mockCache);
        $textDomain = $loader->load($locale, $textDomain);

        self::assertInstanceOf(TextDomain::class, $textDomain);
        self::assertSame($actualMessages, $textDomain->getArrayCopy());
    }

    /**
     * test loading translations with exception
     */
    public function testLoadTranslationsException()
    {
        $initialExceptionMsg = 'initial message';
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(sprintf(TranslationLoader::ERR_UNABLE_TO_LOAD, $initialExceptionMsg));

        $locale = 'en_GB';
        $textDomain = 'default';
        $cacheIdentifier = CacheEncryption::TRANSLATION_KEY_IDENTIFIER;

        $mockCache = m::mock(CachingQueryService::class);
        $mockCache->expects('handleCustomCache')
            ->with($cacheIdentifier, $locale)
            ->andThrow(new \Exception());

        $loader = new TranslationLoader($mockCache);
        $loader->load($locale, $textDomain);
    }

    /**
     * test loading replacements from the cache
     */
    public function testLoadReplacementsFromCache()
    {
        $cacheIdentifier = CacheEncryption::TRANSLATION_REPLACEMENT_IDENTIFIER;
        $replacements = ['replacements'];

        $mockCache = m::mock(CachingQueryService::class);
        $mockCache->expects('handleCustomCache')
            ->with($cacheIdentifier)
            ->andReturn($replacements);

        $loader = new TranslationLoader($mockCache);
        self::assertSame($replacements, $loader->loadReplacements());
    }

    /**
     * replacements aren't absolutely critical so if an exception is thrown an empty array is produced
     */
    public function testUnableToLoadReplacements()
    {
        $cacheIdentifier = CacheEncryption::TRANSLATION_REPLACEMENT_IDENTIFIER;

        $mockCache = m::mock(CachingQueryService::class);
        $mockCache->expects('handleCustomCache')
            ->with($cacheIdentifier)
            ->andThrow(new \Exception());

        $loader = new TranslationLoader($mockCache);
        self::assertSame([], $loader->loadReplacements());
    }
}
