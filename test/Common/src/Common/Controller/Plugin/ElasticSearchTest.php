<?php

namespace OlcsTest\Controller\Plugin;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use \Common\Controller\Plugin\ElasticSearch;
use Olcs\TestHelpers\ControllerPluginManagerHelper;
use CommonTest\Bootstrap;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\Http\Segment;
use Zend\Mvc\Router\RouteMatch;
use Zend\Mvc\Router\SimpleRouteStack;
use Zend\View\Helper\Placeholder;
use Zend\View\Model\ViewModel;

/**
 * Class ElasticSearchPluginTest
 * @package OlcsTest\Mvc\Controller\Plugin
 */
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

    public function setUp()
    {
        $this->request = m::mock('\Zend\Http\Request');

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
        $this->sm = Bootstrap::getServiceManager();

        $this->pm = m::mock('\Zend\Mvc\Controller\PluginManager[setInvokableClass]')->makePartial();
        $this->pm->setInvokableClass('ElasticSearch', 'Common\Controller\Plugin\ElasticSearch');

        $this->sut = new TestController();
        $this->sut->setEvent($this->event);
        $this->sut->setServiceLocator($this->sm);
        $this->sut->setPluginManager($this->pm);
    }

    public function testInvokeOptionsSet()
    {
        $options = [
            'container_name' => 'testcontainer',
            'layout_template' => 'testlayouttemplate',
            'page_route' => 'testroute'
        ];

        $result = $this->sut->pluginInvoke($options);

        $this->assertEquals($result->getContainerName(), $options['container_name']);
        $this->assertEquals($result->getLayoutTemplate(), $options['layout_template']);
        $this->assertEquals($result->getPageRoute(), $options['page_route']);
    }

    public function testInvokeDefaultOptions()
    {
        $result = $this->sut->pluginInvoke([]);

        $this->assertEquals($result->getContainerName(), 'global_search');
        $this->assertEquals($result->getLayoutTemplate(), 'main-search-results');
        $this->assertEquals($result->getPageRoute(), 'testindex');
    }

    /*
    public function testPostAction()
    {
        $this->sut->postAction();
    }
    */

    /*
    public function testBackAction()
    {
        $this->sut->backAction();
    }
    */

    public function testProcessSearchData()
    {
        $mockForm = m::mock('Zend\Form\Form');
        $mockForm->shouldReceive('setAttribute')->with(
            'action',
            m::type('string')
        );
        $mockForm->shouldReceive('setData')->with(m::type('array'));
        $mockForm->shouldReceive('getObject')->andReturn($this->getMockSearchObjectArray());
        $mockForm->shouldReceive('isValid')->andReturn(true);
        $mockForm->shouldReceive('getData')->andReturn(['index' => 'SEARCHINDEX']);

        $mockContainer = m::mock('Zend\View\Helper\Placeholder\Container');
        $mockContainer->shouldReceive('getValue')->andReturn($mockForm);

        $mockPlaceholder = m::mock('Zend\View\Helper\Placeholder');
        $mockPlaceholder->shouldReceive('getContainer')->with('headerSearch')->andReturn($mockContainer);

        $mockViewHelperManager = m::mock('Zend\Mvc\Service\ViewHelperManagerFactory');
        $mockViewHelperManager->shouldReceive('get')->with('placeholder')->andReturn($mockPlaceholder);

        $this->sm->setService('ViewHelperManager', $mockViewHelperManager);

        $plugin = $this->sut->getPlugin();

        $plugin->processSearchData();
    }

    public function testGetSearchForm()
    {
        $mockForm = m::mock('Zend\Form\Form');
        $mockForm->shouldReceive('setAttribute')->with(
            'action',
            m::type('string')
        );
        $mockForm->shouldReceive('setData');

        $mockContainer = m::mock('Zend\View\Helper\Placeholder\Container');
        $mockContainer->shouldReceive('getValue')->andReturn($mockForm);

        $mockPlaceholder = m::mock('Zend\View\Helper\Placeholder');
        $mockPlaceholder->shouldReceive('getContainer')->with('headerSearch')->andReturn($mockContainer);

        $mockViewHelperManager = m::mock('Zend\Mvc\Service\ViewHelperManagerFactory');
        $mockViewHelperManager->shouldReceive('get')->with('placeholder')->andReturn($mockPlaceholder);

        $this->sm->setService('ViewHelperManager', $mockViewHelperManager);

        $plugin = $this->sut->getPlugin();
        $plugin->setSearchData(['index' => 'SEARCHINDEX', 'search' => 'foo']);

        $result = $plugin->getSearchForm();
        $this->assertSame($result, $mockForm);
    }

    public function testGetFiltersForm()
    {
        $mockForm = m::mock('Zend\Form\Form');
        $mockForm->shouldReceive('setAttribute')->with(
            'action',
            m::type('string')
        );
        $mockForm->shouldReceive('setData');

        $mockContainer = m::mock('Zend\View\Helper\Placeholder\Container');
        $mockContainer->shouldReceive('getValue')->andReturn($mockForm);

        $mockPlaceholder = m::mock('Zend\View\Helper\Placeholder');
        $mockPlaceholder->shouldReceive('getContainer')->with('searchFilter')->andReturn($mockContainer);

        $mockViewHelperManager = m::mock('Zend\Mvc\Service\ViewHelperManagerFactory');
        $mockViewHelperManager->shouldReceive('get')->with('placeholder')->andReturn($mockPlaceholder);

        $this->sm->setService('ViewHelperManager', $mockViewHelperManager);

        $plugin = $this->sut->getPlugin();
        $plugin->setSearchData(['index' => 'SEARCHINDEX', 'search' => 'foo']);

        $result = $plugin->getFiltersForm();
        $this->assertSame($result, $mockForm);
    }

    public function testSearchAction()
    {
        $mockForm = m::mock('Zend\Form\Form');
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

        $mockContainer = m::mock('Zend\View\Helper\Placeholder\Container');
        $mockContainer->shouldReceive('getValue')->andReturn($mockForm);

        $mockPlaceholder = m::mock('Zend\View\Helper\Placeholder');
        $mockPlaceholder->shouldReceive('getContainer')->with(m::type('string'))->andReturn($mockContainer);

        $mockViewHelperManager = m::mock('Zend\Mvc\Service\ViewHelperManagerFactory');
        $mockViewHelperManager->shouldReceive('get')->with('placeholder')->andReturn($mockPlaceholder);

        $this->sm->setService('ViewHelperManager', $mockViewHelperManager);

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

    public function testGenerateNavigation()
    {
        $mockSearchTypeService = m::mock('Olcs\Service\Data\Search\SearchType');
        $mockSearchService = m::mock('Common\Service\Data\Search\Search');

        $mockSearchTypeService->shouldReceive('getNavigation')->with(
            'internal-search',
            ['search' => 'foo']
        )->andReturn('MOCKINDEXES');

        $plugin = $this->sut->getPlugin();
        $plugin->setSearchTypeService($mockSearchTypeService);
        $plugin->setSearchService($mockSearchService);
        $plugin->setSearchData(['search' => 'foo']);

        $view = new ViewModel();
        $result = $plugin->generateNavigation($view);

        $this->assertSame($result, $view);
        $this->assertEquals($result->indexes, 'MOCKINDEXES');
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
        $mockForm = m::mock('Zend\Form\Form');
        $mockForm->shouldReceive('getObject')->andReturn($this->getMockSearchObjectArray());

        $mockContainer = m::mock('Zend\View\Helper\Placeholder\Container');
        $mockContainer->shouldReceive('getValue')->andReturn($mockForm);

        $mockPlaceholder = m::mock('Zend\View\Helper\Placeholder');
        $mockPlaceholder->shouldReceive('getContainer')->with('headerSearch')->andReturn($mockContainer);

        $mockViewHelperManager = m::mock('Zend\Mvc\Service\ViewHelperManagerFactory');
        $mockViewHelperManager->shouldReceive('get')->with('placeholder')->andReturn($mockPlaceholder);

        $this->sm->setService('ViewHelperManager', $mockViewHelperManager);

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

    public function testGetSetLayoutTemplate()
    {
        $plugin = $this->sut->pluginInvoke([]);

        $plugin->setLayoutTemplate('testLayoutTemplate');
        $this->assertEquals('testLayoutTemplate', $plugin->getLayoutTemplate());
    }

    public function testGetSetPageRoute()
    {
        $plugin = $this->sut->pluginInvoke([]);

        $plugin->setPageRoute('testpageroute');
        $this->assertEquals('testpageroute', $plugin->getPageRoute());
    }
}

/**
 * Class TestController
 * @package OlcsTest\Controller\Plugin
 */
class TestController extends \Common\Controller\AbstractActionController
{
    public function pluginInvoke($options)
    {
        $plugin = $this->ElasticSearch($options);

        return $plugin;
    }

    public function getPlugin()
    {
        $plugin = $this->ElasticSearch();

        return $plugin;
    }

    public function renderView($view, $pageTitle = null, $pageSubTitle = null)
    {
        $view->pageTitle = $pageTitle;
        $view->pageSubTitle = $pageSubTitle;

        return $view;
    }
}
