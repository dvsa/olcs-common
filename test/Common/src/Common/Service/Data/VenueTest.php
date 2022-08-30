<?php

namespace OlcsTest\Service\Data;

use Common\Exception\DataServiceException;
use Common\Service\Data\Venue;
use Common\Service\Data\Licence as LicenceDataService;
use Mockery as m;
use CommonTest\Service\Data\AbstractDataServiceTestCase;
use Dvsa\Olcs\Transfer\Query\Venue\VenueList as Qry;

/**
 * Class Venue Test
 * @package CommonTest\Service
 */
class VenueTest extends AbstractDataServiceTestCase
{
    /** @var Venue */
    private $sut;

    /** @var LicenceDataService */
    protected $licenceDataService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->licenceDataService = m::mock(LicenceDataService::class);

        $this->sut = new Venue(
            $this->abstractDataServiceServices,
            $this->licenceDataService
        );
    }

    public function testFormatData()
    {
        $source = $this->getSingleSource();
        $expected = $this->getSingleExpected();

        $this->assertEquals($expected, $this->sut->formatData($source));
    }

    /**
     * @dataProvider provideFetchListOptions
     * @param $input
     * @param $expected
     */
    public function testFetchListOptions($input, $expected)
    {
        $this->licenceDataService->shouldReceive('fetchLicenceData')
            ->once()
            ->andReturn(
                [
                    'id' => 7,
                    'niFlag'=> true,
                    'goodsOrPsv' => ['id'=>'lcat_gv'],
                    'trafficArea' => ['id' => 'B']
                ]
            );

        $this->sut->setData('Venue', $input);

        $this->assertEquals($expected, $this->sut->fetchListOptions(''));
    }

    public function provideFetchListOptions()
    {
        return [
            [$this->getSingleSource(), $this->getSingleExpected()],
            [false, []]
        ];
    }

    /**
     * @dataProvider provideFetchListData
     * @param $input
     * @param $expectedTrafficArea
     */
    public function testFetchListData($input, $expectedTrafficArea)
    {
        $results = ['results' => 'results'];

        $this->transferAnnotationBuilder->shouldReceive('createQuery')
            ->with(m::type(Qry::class))
            ->once()
            ->andReturnUsing(
                function ($dto) use ($expectedTrafficArea) {
                    $this->assertEquals($expectedTrafficArea, $dto->getTrafficArea());
                    return $this->query;
                }
            );

        $mockResponse = m::mock()
            ->shouldReceive('isOk')
            ->andReturn(true)
            ->once()
            ->shouldReceive('getResult')
            ->andReturn($results)
            ->twice()
            ->getMock();

        $this->mockHandleQuery($mockResponse);

        $this->assertEquals($results['results'], $this->sut->fetchListData($input));
    }

    public function provideFetchListData()
    {
        return [
            [['trafficArea' => 'B'], 'B'],
            [[], null]
        ];
    }

    public function testFetchLicenceDataWithException()
    {
        $this->expectException(DataServiceException::class);

        $this->transferAnnotationBuilder->shouldReceive('createQuery')
            ->with(m::type(Qry::class))
            ->once()
            ->andReturn($this->query);

        $mockResponse = m::mock()
            ->shouldReceive('isOk')
            ->andReturn(false)
            ->once()
            ->getMock();

        $this->mockHandleQuery($mockResponse);

        $this->sut->fetchListData(['trafficArea' => 'B']);
    }

    /**
     * @return array
     */
    protected function getSingleExpected()
    {
        $expected = [
            'val-1' => 'Value 1',
            'val-2' => 'Value 2',
            'val-3' => 'Value 3',
        ];
        return $expected;
    }

    /**
     * @return array
     */
    protected function getSingleSource()
    {
        $source = [
            ['id' => 'val-1', 'name' => 'Value 1'],
            ['id' => 'val-2', 'name' => 'Value 2'],
            ['id' => 'val-3', 'name' => 'Value 3'],
        ];
        return $source;
    }
}
