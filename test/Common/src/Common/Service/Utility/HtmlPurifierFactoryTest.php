<?php

namespace CommonTest\Service\Helper;

use Common\Service\Utility\HtmlPurifierFactory;
use HTMLPurifier;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;
use Laminas\ServiceManager\ServiceLocatorInterface;

class HtmlPurifierFactoryTest extends TestCase
{
    public function testCreateService()
    {
        /** @var ServiceLocatorInterface|m\MockInterface $mockSl */
        $mockSl = m::mock(ServiceLocatorInterface::class);

        $mockSl->shouldReceive('get')
            ->with('Config')
            ->once()
            ->andReturn(['html-purifier-cache-dir' => 'path']);

        static::assertInstanceOf(
            HtmlPurifier::class,
            (new HtmlPurifierFactory())->createService($mockSl)
        );
    }
}
