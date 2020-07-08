<?php

namespace CommonTest\View\Helper;

use Common\View\Helper\PageTitle;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\I18n\View\Helper\Translate;
use Zend\Mvc\Router\Http\RouteMatch;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\Helper\Placeholder;
use Zend\View\Helper\ViewModel;
use Zend\View\HelperPluginManager;

/**
 * @covers Common\View\Helper\PageTitle
 */
class PageTitleTest extends MockeryTestCase
{
    /**
     * @var PageTitle
     */
    private $sut;

    public function setUp()
    {
        $this->sut = new PageTitle();
    }

    /**
     * @dataProvider providerInvoke
     */
    public function testInvoke($pageTitlePlaceholder, $matchedRouteName, $keyToTranslate)
    {
        $placeholder = m::mock(Placeholder::class);
        $placeholder->shouldReceive('getContainer')->with('pageTitle')->andReturn($pageTitlePlaceholder);

        $translate = m::mock(Translate::class);
        $translate->shouldReceive('__invoke')->with($keyToTranslate)->andReturn('translated');

        $routeMatch = m::mock(RouteMatch::class);
        $routeMatch->shouldReceive('getMatchedRouteName')->andReturn($matchedRouteName);
        $routeMatch->shouldReceive('getParam')->with('action')->andReturn('someaction');

        $app = m::mock();
        $app->shouldReceive('getMvcEvent->getRouteMatch')->andReturn($routeMatch);

        /** @var ServiceLocatorInterface | m\MockInterface $sm */
        $sm = m::mock(ServiceLocatorInterface::class);
        $sm->shouldReceive('get')->with('Application')->andReturn($app);

        /** @var HelperPluginManager | m\MockInterface $vhm */
        $vhm = m::mock(HelperPluginManager::class)->makePartial();
        $vhm->setServiceLocator($sm);
        $vhm->shouldReceive('get')->with('translate')->andReturn($translate);
        $vhm->shouldReceive('get')->with('placeholder')->andReturn($placeholder);

        $sut = $this->sut->createService($vhm);

        $this->assertEquals('translated', $sut->__invoke());
    }

    public function providerInvoke()
    {
        return [
            'placeholder' => [
                'placeholder',
                'foo/bar',
                'placeholder',
            ],
            'routingWithTranslation' => [
                null,
                'foo/bar',
                'page.title.foo/bar.someaction',
            ],
            'routingWithoutTranslation' => [
                null,
                null,
                null,
            ],
        ];
    }
}
