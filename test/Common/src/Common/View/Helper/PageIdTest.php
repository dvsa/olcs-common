<?php

/**
 * Page Id Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\View\Helper;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\View\Helper\PageId;
use Zend\Mvc\Router\Http\RouteMatch;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\HelperPluginManager;

/**
 * Page Id Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class PageIdTest extends MockeryTestCase
{
    /**
     * @var PageId
     */
    private $sut;

    public function setUp(): void
    {
        $this->sut = new PageId();
    }

    public function testInvoke()
    {
        $routeMatch = m::mock(RouteMatch::class);
        $routeMatch->shouldReceive('getMatchedRouteName')->andReturn('foo/bar');
        $routeMatch->shouldReceive('getParam')->with('action')->andReturn('someaction');

        $sm = m::mock(ServiceLocatorInterface::class);
        $sm->shouldReceive('get->getMvcEvent->getRouteMatch')
            ->andReturn($routeMatch);

        $vhm = m::mock(HelperPluginManager::class)->makePartial();
        $vhm->setServiceLocator($sm);

        $sut = $this->sut->createService($vhm);

        $this->assertEquals('pg:foo/bar:someaction', $sut());
    }
}
