<?php

namespace CommonTest\Common\Service\Data\Search;

use Common\Service\Data\Search\Search;
use Common\Service\Data\Search\SearchTypeManager;
use Common\Service\Table\TableFactory;
use Common\Util\RestClient;
use CommonTest\Common\Service\Data\Search\Asset\SearchType;
use Laminas\Http\Request as HttpRequest;
use Laminas\Stdlib\ArrayObject;
use Laminas\View\HelperPluginManager as ViewHelperManager;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Class SearchTest
 * @package OlcsTest\Service\Data\Search
 */
class SearchTest extends MockeryTestCase
{
    /** @var  m\MockInterface */
    private $tableService;

    /** @var  m\MockInterface */
    private $viewHelperManager;

    /** @var  m\MockInterface */
    private $searchTypeManager;

    /** @var Search */
    private $sut;

    protected function setUp(): void
    {
        $this->tableService = m::mock(TableFactory::class);
        $this->viewHelperManager = m::mock(ViewHelperManager::class);
        $this->searchTypeManager = m::mock(SearchTypeManager::class);

        $this->sut = new Search(
            $this->tableService,
            $this->viewHelperManager,
            $this->searchTypeManager
        );
    }

    /**
     * @dataProvider provideGetLimit
     */
    public function testGetLimit($query, $expected): void
    {
        $this->sut->setQuery($query);

        $this->assertEquals($expected, $this->sut->getLimit());
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
     */
    public function testGetPage($query, $expected): void
    {
        $this->sut->setQuery($query);

        $this->assertEquals($expected, $this->sut->getPage());
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

    public function testFetchResultsTable(): void
    {
        $this->searchTypeManager->shouldReceive('get')->with('application')->andReturn(new SearchType());

        $this->tableService->shouldReceive('buildTable')->andReturn('table');

        $mockRequest = m::mock(HttpRequest::class);
        $mockRequest->shouldReceive('getPost')->withNoArgs()->andReturn(null);

        $this->sut->setData('results', ['results']);
        $this->sut->setIndex('application');
        $this->sut->setRequest($mockRequest);

        $this->assertEquals('table', $this->sut->fetchResultsTable());
    }

    public function testFetchResultsTableNoResults(): void
    {
        $this->searchTypeManager->shouldReceive('get')->with('application')->andReturn(new SearchType());

        $this->tableService->shouldReceive('buildTable')->andReturn('table');

        $mockRequest = m::mock(HttpRequest::class);
        $mockRequest->shouldReceive('getPost')->withNoArgs()->andReturn(null);

        $this->sut->setData('results', false);
        $this->sut->setIndex('application');
        $this->sut->setRequest($mockRequest);

        $this->assertEquals('table', $this->sut->fetchResultsTable());
    }

    public function testFetchResults(): void
    {
        $index = 'INDEX_NAME';

        $mockRestClient = m::mock(RestClient::class);
        $mockRestClient->shouldReceive('get')->once()->andReturnUsing(
            function ($uri) {
                // This is the main assertion that test the uri is generated correctly
                $this->assertSame('INDEX_NAME?q=SEARCH&limit=10&page=1&sort=field_name&order=desc', $uri);
                return ['Filters' => []];
            }
        );

        $mockIndex = m::mock();
        $mockIndex->shouldReceive('getFilters')->withNoArgs()->andReturn([]);
        $mockIndex->shouldReceive('getDateRanges')->withNoArgs()->andReturn([]);
        $mockIndex->shouldReceive('getSearchIndices')->withNoArgs()->andReturn($index);

        $this->searchTypeManager->shouldReceive('get')->with($index)->andReturn($mockIndex);

        $this->viewHelperManager->shouldReceive('get->getContainer->getValue')->andReturn('FORM');

        $mockRequest = m::mock(HttpRequest::class);
        $mockRequest->shouldReceive('getPost')->withNoArgs()->andReturn([]);
        $mockRequest->shouldReceive('getQuery')->withNoArgs()->andReturn([]);

        $this->sut->setIndex($index);
        $this->sut->setRequest($mockRequest);
        $this->sut->setQuery(new \ArrayObject(['sort' => ['order' => 'field_name-desc']], \ArrayObject::ARRAY_AS_PROPS));
        $this->sut->setRestClient($mockRestClient);
        $this->sut->setSearch('SEARCH');
        $this->sut->fetchResults();
    }

    public function testFetchResultsNoSortOrder(): void
    {
        $index = 'INDEX_NAME';

        $mockRestClient = m::mock(RestClient::class);
        $mockRestClient->shouldReceive('get')->once()->andReturnUsing(
            function ($uri) {
                // This is the main assertion that test the uri is generated correctly
                $this->assertSame('INDEX_NAME?q=SEARCH&limit=10&page=1&sort=&order=', $uri);
                return ['Filters' => []];
            }
        );

        $mockIndex = m::mock();
        $mockIndex->shouldReceive('getFilters')->withNoArgs()->andReturn([]);
        $mockIndex->shouldReceive('getDateRanges')->withNoArgs()->andReturn([]);
        $mockIndex->shouldReceive('getSearchIndices')->withNoArgs()->andReturn($index);

        $this->searchTypeManager->shouldReceive('get')->with($index)->andReturn($mockIndex);

        $this->viewHelperManager->shouldReceive('get->getContainer->getValue')->andReturn('FORM');

        $mockRequest = m::mock(HttpRequest::class);
        $mockRequest->shouldReceive('getPost')->with()->andReturn([]);
        $mockRequest->shouldReceive('getQuery')->with()->andReturn([]);

        $this->sut->setIndex($index);
        $this->sut->setRequest($mockRequest);
        $this->sut->setRestClient($mockRestClient);
        $this->sut->setSearch('SEARCH');
        $this->sut->fetchResults();
    }
}
