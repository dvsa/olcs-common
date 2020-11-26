<?php

namespace CommonTest\View\Factory\Helper;

use Common\View\Factory\Helper\EscapeHtmlFactory;
use Common\View\Helper\EscapeHtml;
use HTMLPurifier;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;
use Zend\ServiceManager\ServiceLocatorInterface;

class EscapeHtmlFactoryTest extends TestCase
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
                        'HtmlPurifier' => m::mock(HtmlPurifier::class)
                    ];
                    return $map[$class];
                }
            );

        static::assertInstanceOf(
            EscapeHtml::class,
            (new EscapeHtmlFactory())->createService($mockSl)
        );
    }
}
