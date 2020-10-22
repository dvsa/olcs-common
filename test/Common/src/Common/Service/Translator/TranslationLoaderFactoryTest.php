<?php

namespace CommonTest\Service\Translator;

use Common\Service\Cqrs\Query\CachingQueryService;
use Common\Service\Translator\TranslationLoader;
use Common\Service\Translator\TranslationLoaderFactory;
use Dvsa\Olcs\Transfer\Util\Annotation\AnnotationBuilder;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\ServiceManager\ServiceLocatorInterface;

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
        $mockAnnotationBuilder = m::mock(AnnotationBuilder::class);

        $parentSl = m::mock(ServiceLocatorInterface::class);
        $parentSl->expects('get')->with('QueryService')->andReturn($mockQueryService);
        $parentSl->expects('get')->with('TransferAnnotationBuilder')->andReturn($mockAnnotationBuilder);

        $mockSl = m::mock(ServiceLocatorInterface::class);
        $mockSl->expects('getServiceLocator')->withNoArgs()->andReturn($parentSl);

        $sut = new TranslationLoaderFactory();
        $service = $sut->createService($mockSl);

        self::assertInstanceOf(TranslationLoader::class, $service);
    }
}
