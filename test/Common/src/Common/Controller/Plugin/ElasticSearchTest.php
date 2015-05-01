<?php

namespace OlcsTest\Controller\Plugin;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use \Common\Controller\Plugin\ElasticSearch;
use Olcs\TestHelpers\ControllerPluginManagerHelper;
use CommonTest\Bootstrap;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\RouteMatch;
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

        $this->routeMatch = new RouteMatch([]);
        $this->event = new MvcEvent();
        $this->event->setRouteMatch($this->routeMatch);
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
        $this->assertEquals($result->getPageRoute(), NULL);
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

    /*
    public function testProcessSearchData()
    {
        $this->sut->getProcessSearchData();
    }
    */

    /*
    public function testGetSearchForm()
    {
        $this->sut->getSearchForm();
    }
    */

    /*
    public function testGetFiltersForm()
    {
        $this->sut->getFiltersForm();
    }
    */

    /*
    public function testSearchAction()
    {
        $this->sut->searchAction();
    }
    */

    /*
    public function testSetNavigationCurrentLocation()
    {
        $this->sut->setNavigationCurrentLocation();
    }
    */

    /*
    public function testExtractSearchData()
    {
        $this->sut->extractSearchData();
    }
    */

    /*
    public function testGenerateNavigation()
    {
        $this->sut->generateResults();
    }
    */

    private function getMockSearchObjectArray()
    {
        return [
            'index' => 'foo'
        ];
    }
/*
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

        $plugin = $this->sut->pluginGenerateResults();


    }*/

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

class testController extends \Common\Controller\AbstractActionController
{
    public function pluginInvoke($options)
    {
        $plugin = $this->ElasticSearch($options);

        return $plugin;
    }

    public function pluginGenerateResults()
    {
        $view = new ViewModel();
        $plugin = $this->ElasticSearch();

        return $plugin->generateResults($view);
    }
}
