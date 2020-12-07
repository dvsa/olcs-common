<?php

namespace CommonTest\View\Factory\Helper;

use Common\Service\Cqrs\Query\CachingQueryService;
use Common\View\Factory\Helper\SystemInfoMessagesFactory;
use Common\View\Helper\SystemInfoMessages;
use Dvsa\Olcs\Transfer\Util\Annotation\AnnotationBuilder;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;
use Laminas\ServiceManager\ServiceLocatorInterface;
use ZfcRbac\Service\AuthorizationService;

/**
 * @covers Common\View\Factory\Helper\SystemInfoMessagesFactory
 */
class SystemInfoMessagesFactoryTest extends TestCase
{
    public function testCreateService()
    {
        /** @var ServiceLocatorInterface|m\MockInterface $mockSl */
        $mockSl = m::mock(ServiceLocatorInterface::class);
        $mockSl->shouldReceive('getServiceLocator')->andReturnSelf();
        $mockSl->shouldReceive('get')
            ->andReturnUsing(
                function ($class) {
                    $map = [
                        'QueryService' => m::mock(CachingQueryService::class),
                        'TransferAnnotationBuilder' => m::mock(AnnotationBuilder::class),
                    ];

                    return $map[$class];
                }
            );

        static::assertInstanceOf(
            SystemInfoMessages::class,
            (new SystemInfoMessagesFactory())->createService($mockSl)
        );
    }
}
