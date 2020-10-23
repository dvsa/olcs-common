<?php

namespace CommonTest\Service\Translator;

use Common\Service\Cqrs\Query\CachingQueryService;
use Common\Service\Translator\TranslationLoader;
use Dvsa\Olcs\Transfer\Query\QueryContainerInterface;
use Dvsa\Olcs\Transfer\Query\TranslationCache\Key;
use Dvsa\Olcs\Transfer\Service\CacheEncryption;
use Dvsa\Olcs\Transfer\Util\Annotation\AnnotationBuilder;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\Http\Response;
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
    public function testLoadFromCache()
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

        $mockAnnotationBuilder = m::mock(AnnotationBuilder::class);

        $loader = new TranslationLoader($mockCache, $mockAnnotationBuilder);
        $textDomain = $loader->load($locale, $textDomain);

        self::assertInstanceOf(TextDomain::class, $textDomain);
        self::assertSame($actualMessages, $textDomain->getArrayCopy());
    }

    /**
     * if the Redis cache is empty, we load from the database
     */
    public function testLoadFromDatabase()
    {
        $locale = 'en_GB';
        $textDomain = 'default';
        $actualMessages = ['some_key' => 'some_text'];
        $cacheIdentifier = CacheEncryption::TRANSLATION_KEY_IDENTIFIER;
        $cqrsQueryContainer = m::mock(QueryContainerInterface::class);

        $messages = [
            $textDomain => [
                $locale => $actualMessages,
            ],
        ];

        $mockAnnotationBuilder = m::mock(AnnotationBuilder::class);
        $mockAnnotationBuilder->expects('createQuery')
            ->with(m::type(Key::class))
            ->andReturn($cqrsQueryContainer);

        $response = m::mock(Response::class);
        $response->expects('isOk')->withNoArgs()->andReturnTrue();
        $response->expects('getResult')->withNoArgs()->andReturn($messages);

        $mockCache = m::mock(CachingQueryService::class);
        $mockCache->expects('handleCustomCache')
            ->with($cacheIdentifier, $locale)
            ->andReturnFalse();
        $mockCache->expects('send')->with($cqrsQueryContainer)->andReturn($response);

        $loader = new TranslationLoader($mockCache, $mockAnnotationBuilder);
        $textDomain = $loader->load($locale, $textDomain);

        self::assertInstanceOf(TextDomain::class, $textDomain);
        self::assertSame($actualMessages, $textDomain->getArrayCopy());
    }

    /**
     * if the cache throws an exception we still go to the database having logged the error
     */
    public function testCacheExceptionThenLoadFromDatabase()
    {
        $locale = 'en_GB';
        $textDomain = 'default';
        $actualMessages = ['some_key' => 'some_text'];
        $cacheIdentifier = CacheEncryption::TRANSLATION_KEY_IDENTIFIER;
        $cqrsQueryContainer = m::mock(QueryContainerInterface::class);
        $cacheExceptionMsg = 'cache exception msg';

        $messages = [
            $textDomain => [
                $locale => $actualMessages,
            ],
        ];

        $mockAnnotationBuilder = m::mock(AnnotationBuilder::class);
        $mockAnnotationBuilder->expects('createQuery')
            ->with(m::type(Key::class))
            ->andReturn($cqrsQueryContainer);

        $response = m::mock(Response::class);
        $response->expects('isOk')->withNoArgs()->andReturnTrue();
        $response->expects('getResult')->withNoArgs()->andReturn($messages);

        $mockCache = m::mock(CachingQueryService::class);
        $mockCache->expects('handleCustomCache')
            ->with($cacheIdentifier, $locale)
            ->andThrow(\Exception::class, $cacheExceptionMsg);
        $mockCache->expects('send')->with($cqrsQueryContainer)->andReturn($response);

        $loader = new TranslationLoader($mockCache, $mockAnnotationBuilder);
        $textDomain = $loader->load($locale, $textDomain);

        self::assertInstanceOf(TextDomain::class, $textDomain);
        self::assertSame($actualMessages, $textDomain->getArrayCopy());
    }

    /**
     * if both the cache and the database are unavailable, throw an exception
     */
    public function testUnableToLoad()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(TranslationLoader::ERR_UNABLE_TO_LOAD);

        $locale = 'en_GB';
        $textDomain = 'default';
        $cacheIdentifier = CacheEncryption::TRANSLATION_KEY_IDENTIFIER;
        $cqrsQueryContainer = m::mock(QueryContainerInterface::class);

        $mockAnnotationBuilder = m::mock(AnnotationBuilder::class);
        $mockAnnotationBuilder->expects('createQuery')
            ->with(m::type(Key::class))
            ->andReturn($cqrsQueryContainer);

        $response = m::mock(Response::class);
        $response->expects('isOk')->withNoArgs()->andReturnFalse();
        $response->expects('getResult')->never();

        $mockCache = m::mock(CachingQueryService::class);
        $mockCache->expects('handleCustomCache')
            ->with($cacheIdentifier, $locale)
            ->andReturnNull();
        $mockCache->expects('send')->with($cqrsQueryContainer)->andReturn($response);

        $loader = new TranslationLoader($mockCache, $mockAnnotationBuilder);
        $loader->load($locale, $textDomain);
    }
}
