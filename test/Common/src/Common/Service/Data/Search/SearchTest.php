<?php

namespace CommonTest\Service\Data\Search;

use Common\Service\Data\Search\Search;
use Common\Service\Data\Search\SearchTypeManager;
use CommonTest\Service\Data\Search\Asset\SearchType;
use Laminas\Stdlib\ArrayObject;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Laminas\Http\Request as HttpRequest;

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

        $mockStm = m::mock('Laminas\ServiceManager\ServiceLocatorInterface');
        $mockStm->shouldReceive('getRegisteredServices')->andReturn($servicesArray);
        $mockStm->shouldReceive('get')->with('application')->andReturn(new SearchType());
        $mockStm->shouldReceive('get')->with('licence')->andReturn(new SearchType());

        return $mockStm;
    }

    public function testFetchResultsTable()
    {
        $mockTableBuilder = m::mock('Common\Service\Table\TableBuilder');
        $mockTableBuilder->shouldReceive('buildTable')->andReturn('table');

        $mockSl = m::mock('Laminas\ServiceManager\ServiceLocatorInterface');
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

    public function testFetchResultsTableNoResults()
    {
        $mockTableBuilder = m::mock('Common\Service\Table\TableBuilder');
        $mockTableBuilder->shouldReceive('buildTable')->andReturn('table');

        $mockSl = m::mock('Laminas\ServiceManager\ServiceLocatorInterface');
        $mockSl->shouldReceive('get')
            ->with(SearchTypeManager::class)
            ->andReturn($this->getMockSearchTypeManager());

        $mockRequest = m::mock(get_class(new HttpRequest));
        $mockRequest->shouldReceive('getPost')->with()->andReturn(null);

        $mockSl->shouldReceive('get')->with('Table')->andReturn($mockTableBuilder);

        $sut = new Search();
        $sut->setData('results', false);
        $sut->setServiceLocator($mockSl);
        $sut->setIndex('application');
        $sut->setRequest($mockRequest);

        $this->assertEquals('table', $sut->fetchResultsTable());
    }

    public function testFetchResults()
    {
        $index = 'INDEX_NAME';

        $mockRestClient = m::mock(\Common\Util\RestClient::class);
        $mockRestClient->shouldReceive('get')->once()->andReturnUsing(
            function ($uri) {
                // This is the main assertion that test the uri is generated correctly
                $this->assertSame('INDEX_NAME?q=SEARCH&limit=10&page=1&sort=field_name&order=desc', $uri);
                return ['Filters' => []];
            }
        );

        $mockIndex = m::mock();
        $mockIndex->shouldReceive('getFilters')->with()->andReturn([]);
        $mockIndex->shouldReceive('getDateRanges')->with()->andReturn([]);
        $mockIndex->shouldReceive('getSearchIndices')->with()->andReturn($index);

        $mockSearchManager = $this->getMockSearchTypeManager();
        $mockSearchManager->shouldReceive('get')->with($index)->andReturn($mockIndex);

        $mockViewHelperManager = m::mock();
        $mockViewHelperManager->shouldReceive('get->getContainer->getValue')->andReturn('FORM');

        $mockSl = m::mock('Laminas\ServiceManager\ServiceLocatorInterface');
        $mockSl->shouldReceive('get')->with(SearchTypeManager::class)->andReturn($mockSearchManager);
        $mockSl->shouldReceive('get')->with('ViewHelperManager')->andReturn($mockViewHelperManager);

        $mockRequest = m::mock(get_class(new HttpRequest));
        $mockRequest->shouldReceive('getPost')->with()->andReturn([]);
        $mockRequest->shouldReceive('getQuery')->with()->andReturn([]);

        $sut = new Search();
        $sut->setServiceLocator($mockSl);
        $sut->setIndex($index);
        $sut->setRequest($mockRequest);
        $sut->setQuery(new \ArrayObject(['sort' => ['order' => 'field_name-desc']], \ArrayObject::ARRAY_AS_PROPS));
        $sut->setRestClient($mockRestClient);

        $sut->setSearch('SEARCH');
        $sut->fetchResults();
    }

    public function testFetchResultsNoSortOrder()
    {
        $index = 'INDEX_NAME';

        $mockRestClient = m::mock(\Common\Util\RestClient::class);
        $mockRestClient->shouldReceive('get')->once()->andReturnUsing(
            function ($uri) {
                // This is the main assertion that test the uri is generated correctly
                $this->assertSame('INDEX_NAME?q=SEARCH&limit=10&page=1&sort=&order=', $uri);
                return ['Filters' => []];
            }
        );

        $mockIndex = m::mock();
        $mockIndex->shouldReceive('getFilters')->with()->andReturn([]);
        $mockIndex->shouldReceive('getDateRanges')->with()->andReturn([]);
        $mockIndex->shouldReceive('getSearchIndices')->with()->andReturn($index);

        $mockSearchManager = $this->getMockSearchTypeManager();
        $mockSearchManager->shouldReceive('get')->with($index)->andReturn($mockIndex);

        $mockViewHelperManager = m::mock();
        $mockViewHelperManager->shouldReceive('get->getContainer->getValue')->andReturn('FORM');

        $mockSl = m::mock('Laminas\ServiceManager\ServiceLocatorInterface');
        $mockSl->shouldReceive('get')->with(SearchTypeManager::class)->andReturn($mockSearchManager);
        $mockSl->shouldReceive('get')->with('ViewHelperManager')->andReturn($mockViewHelperManager);

        $mockRequest = m::mock(get_class(new HttpRequest));
        $mockRequest->shouldReceive('getPost')->with()->andReturn([]);
        $mockRequest->shouldReceive('getQuery')->with()->andReturn([]);

        $sut = new Search();
        $sut->setServiceLocator($mockSl);
        $sut->setIndex($index);
        $sut->setRequest($mockRequest);
        $sut->setRestClient($mockRestClient);

        $sut->setSearch('SEARCH');
        $sut->fetchResults();
    }
}
