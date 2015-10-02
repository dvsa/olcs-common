<?php

/**
 * Get Placeholder Factory Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\View\Helper;

use Common\View\Helper\GetPlaceholder;
use Common\View\Helper\GetPlaceholderFactory;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\Helper\Placeholder;
use Zend\View\Model\ViewModel;

/**
 * Get Placeholder Factory Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class GetPlaceholderFactoryTest extends MockeryTestCase
{
    protected $sut;

    protected $mockPlaceholder;

    public function setUp()
    {
        $this->sut = new GetPlaceholderFactory();

        $this->mockPlaceholder = m::mock(Placeholder::class);

        $sm = m::mock(ServiceLocatorInterface::class);
        $sm->shouldReceive('get')->with('placeholder')->andReturn($this->mockPlaceholder);

        $this->sut->createService($sm);
    }

    public function testInvoke()
    {
        $container = m::mock();

        $this->mockPlaceholder->shouldReceive('__invoke')
            ->with('foo')
            ->andReturn($container);

        /** @var GetPlaceholder $getPlaceholder */
        $getPlaceholder = $this->sut->__invoke('foo');

        $this->assertInstanceOf(GetPlaceholder::class, $getPlaceholder);

        $getPlaceholder2 = $this->sut->__invoke('foo');

        $this->assertSame($getPlaceholder, $getPlaceholder2);
    }
}
