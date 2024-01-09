<?php

namespace CommonTest\Controller\Plugin;

use Laminas\Mvc\Controller\PluginManager;
use Laminas\View\Helper\Placeholder;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Olcs\TestHelpers\ControllerPluginManagerHelper;
use Laminas\Mvc\MvcEvent;
use Laminas\Router\Http\Segment;
use Laminas\Router\RouteMatch;
use Laminas\Router\SimpleRouteStack;
use Laminas\View\Model\ViewModel;
use Laminas\Navigation\Page\Mvc as NavigationPage;
use CommonTest\Common\Controller\Plugin\ControllerStub;

class ElasticSearchTest extends MockeryTestCase
{
    protected $sut;

    protected $controller;

    protected $mockServiceLocator;

    protected $mockPluginManager;

    /**
     * @var ControllerPluginManagerHelper
     */
    protected $pluginManagerHelper;

    public function setUp(): void
    {
        $this->request = m::mock('\Laminas\Http\Request');

        $this->routeMatch = new RouteMatch(['controller' => 'index', 'action' => 'index', 'index' => 'SEARCHINDEX']);
        $this->routeMatch->setMatchedRouteName('testindex');
        $this->event = new MvcEvent();

        $routeStack = new SimpleRouteStack();
        $route = new Segment('/testindex/[:controller/[:action/]]');
        $routeStack->addRoute('testindex', $route);
        $route = new Segment('/dashboard/[:controller/[:action/]]');
        $routeStack->addRoute('dashboard', $route);

        $this->event->setRouter($routeStack);

        $this->event->setRouteMatch($this->routeMatch);
        $this->event->setRequest($this->request);
        $this->sm = m::mock('\Laminas\ServiceManager\ServiceManager')
            ->makePartial()
            ->setAllowOverride(true);

        $this->pm = m::mock(PluginManager::class)->makePartial();
        $this->pm->setInvokableClass('ElasticSearch', 'Common\Controller\Plugin\ElasticSearch');

        $this->mockPlaceholder = m::mock(Placeholder::class);
        $this->sut = new ControllerStub($this->mockPlaceholder);
        $this->sut->setEvent($this->event);
        $this->sut->setServiceLocator($this->sm);
        $this->sut->setPluginManager($this->pm);
    }

    public function testInvokeOptionsSet()
    {
        $options = [
            'container_name' => 'testcontainer',
            'page_route' => 'testroute'
        ];

        $result = $this->sut->pluginInvoke($options);

        $this->assertEquals($result->getContainerName(), $options['container_name']);
        $this->assertEquals($result->getPageRoute(), $options['page_route']);
    }

    public function testInvokeDefaultOptions()
    {
        $result = $this->sut->pluginInvoke([]);

        $this->assertEquals($result->getContainerName(), 'global_search');
        $this->assertEquals($result->getPageRoute(), 'testindex');
    }

    public function testProcessSearchData()
    {
        $mockForm = m::mock('Laminas\Form\Form');
        $mockForm->shouldReceive('setAttribute')->with(
            'action',
            m::type('string')
        );
        $mockForm->shouldReceive('setData')->with(m::type('array'));
        $mockForm->shouldReceive('getObject')->andReturn($this->getMockSearchObjectArray());
        $mockForm->shouldReceive('isValid')->andReturn(true);
        $mockForm->shouldReceive('getData')->andReturn(['index' => 'SEARCHINDEX']);

        $mockContainer = m::mock('Laminas\View\Helper\Placeholder\Container');
        $mockContainer->shouldReceive('getValue')->andReturn($mockForm);

        $this->mockPlaceholder->shouldReceive('getContainer')->with('headerSearch')->andReturn($mockContainer);

        $plugin = $this->sut->getPlugin();

        $plugin->processSearchData();
    }

    public function testGetSearchForm()
    {
        $mockForm = m::mock('Laminas\Form\Form');
        $mockForm->shouldReceive('setAttribute')->with(
            'action',
            m::type('string')
        );
        $mockForm->shouldReceive('setData');

        $mockContainer = m::mock('Laminas\View\Helper\Placeholder\Container');
        $mockContainer->shouldReceive('getValue')->andReturn($mockForm);

        $this->mockPlaceholder->shouldReceive('getContainer')->with('headerSearch')->andReturn($mockContainer);

        $plugin = $this->sut->getPlugin();
        $plugin->setSearchData(['index' => 'SEARCHINDEX', 'search' => 'foo']);

        $result = $plugin->getSearchForm();
        $this->assertSame($result, $mockForm);
    }

    public function testGetFiltersForm()
    {
        $mockForm = m::mock('Laminas\Form\Form');
        $mockForm->shouldReceive('setAttribute')->with(
            'action',
            m::type('string')
        );
        $mockForm->shouldReceive('setData');

        $mockContainer = m::mock('Laminas\View\Helper\Placeholder\Container');
        $mockContainer->shouldReceive('getValue')->andReturn($mockForm);

        $this->mockPlaceholder->shouldReceive('getContainer')->with('searchFilter')->andReturn($mockContainer);

        $plugin = $this->sut->getPlugin();
        $plugin->setSearchData(['index' => 'SEARCHINDEX', 'search' => 'foo']);

        $result = $plugin->getFiltersForm();
        $this->assertSame($result, $mockForm);
    }

    public function testSearchAction()
    {
        $mockForm = m::mock('Laminas\Form\Form');
        $mockForm->shouldReceive('getObject')->andReturn($this->getMockSearchObjectArray());
        $mockForm->shouldReceive('setAttribute')->with(
            'action',
            m::type('string')
        );
        $mockForm->shouldReceive('setData');
        $mockForm->shouldReceive('isValid')->andReturn(true);
        $mockForm->shouldReceive('getData')->andReturn(['index' => 'SEARCHINDEX']);

        $indexes = ['searchindex1', 'searchindex2'];
        $results = ['search-results'];

        $mockSearchTypeService = m::mock('Olcs\Service\Data\Search\SearchType');
        $mockSearchTypeService->shouldReceive('getNavigation')->with(
            m::type('string'),
            m::type('array')
        )->andReturn($indexes);

        $mockQuery = m::mock();
        $mockRequest = m::mock();
        $mockIndex = m::mock();

        $mockQuery->shouldReceive('setRequest')->with(m::type('object'))->andReturn($mockRequest);
        $mockRequest->shouldReceive('setIndex')->with('SEARCHINDEX')->andReturn($mockIndex);
        $mockIndex->shouldReceive('setSearch')->with('SEARCH')->andReturnSelf();

        $mockSearchService = m::mock('Common\Service\Data\Search\Search');
        $mockSearchService->shouldReceive('setQuery')->with(m::type('object'))->andReturn($mockQuery);
        $mockSearchService->shouldReceive('fetchResultsTable')->andReturn($results);

        $mockContainer = m::mock('Laminas\View\Helper\Placeholder\Container');
        $mockContainer->shouldReceive('getValue')->andReturn($mockForm);

        $this->mockPlaceholder->shouldReceive('getContainer')->with('searchFilter')->andReturn($mockContainer);
        $this->mockPlaceholder->shouldReceive('getContainer')->with('headerSearch')->andReturn($mockContainer);

        $plugin = $this->sut->getPlugin();
        $plugin->setSearchData(['index' => 'SEARCHINDEX', 'search' => 'foo']);
        $plugin->setSearchTypeService($mockSearchTypeService);
        $plugin->setSearchService($mockSearchService);

        $resultView = $plugin->searchAction();

        $this->assertEquals($resultView->indexes, $indexes);
        $this->assertEquals($resultView->results, $results);
    }

    public function testSetNavigationCurrentLocation()
    {
        $plugin = $this->sut->getPlugin();
        $plugin->navigationId = 'home';

        $mockNavigationService = m::mock('Common\Service\Data\Search\Search');
        $mockNavigationService->shouldReceive('findOneBy')->with('id', 'home')->andReturnSelf();
        $mockNavigationService->shouldReceive('setActive');
        $plugin->setNavigationService($mockNavigationService);

        $this->assertTrue($plugin->setNavigationCurrentLocation());
    }

    public function testExtractSearchData()
    {
        $plugin = $this->sut->getPlugin();

        $result = $plugin->extractSearchData();

        $this->assertArrayHasKey('index', $result);
        $this->assertEquals($result['index'], 'SEARCHINDEX');
    }

    public function testConfigureNavigation()
    {
        $mockSearchTypeService = m::mock('Olcs\Service\Data\Search\SearchType');
        $mockSearchService = m::mock('Common\Service\Data\Search\Search');

        $mi = m::mock('Laminas\Navigation\Navigation');
        $mi->shouldReceive('findOneBy')->with('id', 'search-da')->andReturnSelf();
        $mi->shouldReceive('setActive')->with(true)->andReturnNull();

        $mockSearchTypeService->shouldReceive('getNavigation')->with(
            'internal-search',
            ['search' => 'foo']
        )->andReturn($mi);

        $page = m::mock(NavigationPage::class);

        $mi->shouldReceive('findOneBy')->with('id', 'remove-id')->once()->andReturn($page);
        $mi->shouldReceive('removePage')->with($page, true)->once()->andReturnTrue();

        $this->mockPlaceholder->shouldReceive('getContainer')->with('horizontalNavigationContainer')->andReturn(m::mock()->shouldReceive('set')->once()->with($mi)->getMock());

        $plugin = $this->sut->getPlugin();
        $plugin->setSearchTypeService($mockSearchTypeService);
        $plugin->setSearchService($mockSearchService);
        $plugin->setSearchData(['search' => 'foo', 'index' => 'da']);

        $view = new ViewModel();
        $plugin->configureNavigation(['remove-id']);
    }

    private function getMockSearchObjectArray()
    {
        return [
            'index' => 'foo',
            'search' => 'SEARCH'
        ];
    }

    public function testGenerateResults()
    {
        $mockForm = m::mock('Laminas\Form\Form');
        $mockForm->shouldReceive('getObject')->andReturn($this->getMockSearchObjectArray());

        $mockContainer = m::mock('Laminas\View\Helper\Placeholder\Container');
        $mockContainer->shouldReceive('getValue')->andReturn($mockForm);

        $this->mockPlaceholder->shouldReceive('getContainer')->with('headerSearch')->andReturn($mockContainer);

        $mockSearchTypeService = m::mock('Olcs\Service\Data\Search\SearchType');
        $mockSearchService = m::mock('Common\Service\Data\Search\Search');

        $mockQuery = m::mock();
        $mockRequest = m::mock();
        $mockIndex = m::mock();

        $mockQuery->shouldReceive('setRequest')->with(m::type('object'))->andReturn($mockRequest);
        $mockRequest->shouldReceive('setIndex')->with('SEARCHINDEX')->andReturn($mockIndex);
        $mockIndex->shouldReceive('setSearch')->with('SEARCH')->andReturnSelf();

        $mockSearchService->shouldReceive('setQuery')->with(m::type('object'))->andReturn($mockQuery);
        $mockSearchService->shouldReceive('fetchResultsTable')->andReturn('RESULTS');

        $plugin = $this->sut->getPlugin();
        $plugin->setSearchTypeService($mockSearchTypeService);
        $plugin->setSearchService($mockSearchService);

        $view = new ViewModel();
        $result = $plugin->generateResults($view);

        $this->assertEquals($result->results, 'RESULTS');
    }

    public function testGetSetContainerName()
    {
        $plugin = $this->sut->pluginInvoke([]);

        $plugin->setContainerName('testContainerName');
        $this->assertEquals('testContainerName', $plugin->getContainerName());
    }

    public function testGetSetSearchData()
    {
        $plugin = $this->sut->pluginInvoke([]);

        $plugin->setSearchData('testsearchdata');
        $this->assertEquals('testsearchdata', $plugin->getSearchData());
    }

    public function testGetSetPageRoute()
    {
        $plugin = $this->sut->pluginInvoke([]);

        $plugin->setPageRoute('testpageroute');
        $this->assertEquals('testpageroute', $plugin->getPageRoute());
    }
}
