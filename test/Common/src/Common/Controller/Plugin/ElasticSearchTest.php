<?php

namespace OlcsTest\Controller\Plugin;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use \Common\Controller\Plugin\ElasticSearch;
use Olcs\TestHelpers\ControllerPluginManagerHelper;

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
        $this->sut = new ElasticSearch();
    }


/*    public function testInvokeOptionsSet()
    {
        $controller = new TestController();
        $controller->getPluginManager()
            ->setInvokableClass('ElasticSearch', '\Common\Controller\Plugin\ElasticSearch');

        $options = [
            'container_name' => 'testcontainer',
            'layout_template' => 'testlayouttemplate',
            'page_route' => 'testroute'
        ];

        $mockParams = m::mock('Zend/Mvc/Controller/Plugin/Params');
        $mockParams->shouldReceive('getParams');

        $result = $controller->invokeAction($options);

        $this->assertEquals($result->getContainerName(), $options['container_name']);

    }*/


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

    /*
    public function testGenerateResults()
    {
        $this->sut->generateResults();
    }
    */

    public function testGetSetContainerName()
    {
        $this->sut->setContainerName('testContainerName');
        $this->assertEquals('testContainerName', $this->sut->getContainerName());
    }

    public function testGetSetSearchData()
    {
        $this->sut->setSearchData('testsearchdata');
        $this->assertEquals('testsearchdata', $this->sut->getSearchData());
    }

    public function testGetSetLayoutTemplate()
    {
        $this->sut->setLayoutTemplate('testLayoutTemplate');
        $this->assertEquals('testLayoutTemplate', $this->sut->getLayoutTemplate());
    }

    public function testGetSetPageRoute()
    {
        $this->sut->setPageRoute('testpageroute');
        $this->assertEquals('testpageroute', $this->sut->getPageRoute());
    }

}

class testController extends \Common\Controller\AbstractActionController
{
    public function invokeAction($options)
    {
        $plugin = $this->ElasticSearch($options);

        return $plugin;
    }
}
