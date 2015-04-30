<?php

namespace OlcsTest\Controller\Plugin;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use \Common\Controller\Plugin\ElasticSearch;

/**
 * Class ElasticSearchPluginTest
 * @package OlcsTest\Mvc\Controller\Plugin
 */
class ElasticSearchTest extends \MockeryTestCase
{
    protected $sut;

    public function setUp()
    {
        $this->sut = new ElasticSearch();
    }

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
