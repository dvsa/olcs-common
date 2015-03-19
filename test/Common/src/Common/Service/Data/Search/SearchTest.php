<?php

namespace CommonTest\Service\Data\Search;

use Common\Service\Data\Search\Search;
use Common\Service\Data\Search\SearchTypeManager;
use CommonTest\Service\Data\Search\Asset\SearchType;
use Zend\Stdlib\ArrayObject;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\Http\Request as HttpRequest;

/**
 * Class SearchTest
 * @package OlcsTest\Service\Data\Search
 */
class SearchTest extends MockeryTestCase
{
    /**
     * @dataProvider provideGetLimit
     * @param $query
     * @param $expected
     */
    public function testGetLimit($query, $expected)
    {
        $sut = new Search();
        $sut->setQuery($query);

        $this->assertEquals($expected, $sut->getLimit());
    }

    public function provideGetLimit()
    {
        $stubQuery = new \ArrayObject();
        $stubQuery->limit = 15;

        return [
            [$stubQuery, 15],
            [new ArrayObject(), 10],
            [null, 10]
        ];
    }

    /**
     * @dataProvider provideGetPage
     * @param $query
     * @param $expected
     */
    public function testGetPage($query, $expected)
    {
        $sut = new Search();
        $sut->setQuery($query);

        $this->assertEquals($expected, $sut->getPage());
    }

    public function provideGetPage()
    {
        $stubQuery = new \ArrayObject();
        $stubQuery->page = 3;

        return [
            [$stubQuery, 3],
            [new ArrayObject(), 1],
            [null, 1]
        ];
    }

    protected function getMockSearchTypeManager()
    {
        $servicesArray = [
            'factories' => [
                'licence'
            ],
            'invokableClasses' => [
                'application'
            ]
        ];

        $mockStm = m::mock('Zend\ServiceManager\ServiceLocatorInterface');
        $mockStm->shouldReceive('getRegisteredServices')->andReturn($servicesArray);
        $mockStm->shouldReceive('get')->with('application')->andReturn(new SearchType());
        $mockStm->shouldReceive('get')->with('licence')->andReturn(new SearchType());

        return $mockStm;
    }

    public function testFetchResultsTable()
    {
        $mockTableBuilder = m::mock('Common\Service\Table\TableBuilder');
        $mockTableBuilder->shouldReceive('buildTable')->andReturn('table');

        $mockSl = m::mock('Zend\ServiceManager\ServiceLocatorInterface');
        $mockSl->shouldReceive('get')
            ->with(SearchTypeManager::class)
            ->andReturn($this->getMockSearchTypeManager());

        $mockRequest = m::mock(get_class(new HttpRequest));
        $mockRequest->shouldReceive('getPost')->with()->andReturn(null);

        $mockSl->shouldReceive('get')->with('Table')->andReturn($mockTableBuilder);

        $sut = new Search();
        $sut->setData('results', ['results']);
        $sut->setServiceLocator($mockSl);
        $sut->setIndex('application');
        $sut->setRequest($mockRequest);

        $this->assertEquals('table', $sut->fetchResultsTable());
    }
}
