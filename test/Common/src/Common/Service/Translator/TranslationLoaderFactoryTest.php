<?php

namespace CommonTest\Service\Translator;

use Common\Service\Cqrs\Query\CachingQueryService;
use Common\Service\Translator\TranslationLoader;
use Common\Service\Translator\TranslationLoaderFactory;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * TranslationLoaderFactoryTest
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class TranslationLoaderFactoryTest extends MockeryTestCase
{
    public function testCreateService()
    {
        $mockQueryService = m::mock(CachingQueryService::class);

        $parentSl = m::mock(ServiceLocatorInterface::class);
        $parentSl->expects('get')->with('QueryService')->andReturn($mockQueryService);

        $sut = new TranslationLoaderFactory();
        $service = $sut->createService($parentSl);

        self::assertInstanceOf(TranslationLoader::class, $service);
    }
}
