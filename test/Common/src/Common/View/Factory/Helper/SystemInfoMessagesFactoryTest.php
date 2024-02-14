<?php

namespace CommonTest\View\Factory\Helper;

use Common\Service\Cqrs\Query\CachingQueryService;
use Common\View\Factory\Helper\SystemInfoMessagesFactory;
use Common\View\Helper\SystemInfoMessages;
use Dvsa\Olcs\Transfer\Util\Annotation\AnnotationBuilder;
use Psr\Container\ContainerInterface;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;

/**
 * @covers Common\View\Factory\Helper\SystemInfoMessagesFactory
 */
class SystemInfoMessagesFactoryTest extends TestCase
{
    public function testInvoke(): void
    {
        $container = m::mock(ContainerInterface::class);
        $container->expects('get')
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
            (new SystemInfoMessagesFactory())->__invoke($container, SystemInfoMessages::class)
        );
    }
}
