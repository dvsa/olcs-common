<?php

namespace OlcsTest\Service\Data;

use Common\Service\Data\PiVenue;
use Mockery as m;
use Dvsa\Olcs\Transfer\Query\Cases\PiVenue\PiVenueList as Qry;
use Common\Service\Entity\Exceptions\UnexpectedResponseException;
use CommonTest\Service\Data\AbstractDataServiceTestCase;

/**
 * Class PiVenue Test
 * @package CommonTest\Service
 */
class PiVenueTest extends AbstractDataServiceTestCase
{
    public function testFormatData()
    {
        $source = $this->getSingleSource();
        $expected = $this->getSingleExpected();

        $sut = new PiVenue();

        $this->assertEquals($expected, $sut->formatData($source));
    }

    /**
     * @dataProvider provideFetchListOptions
     * @param $input
     * @param $expected
     */
    public function testFetchListOptions($input, $expected)
    {
        $mockLicenceService = m::mock()
            ->shouldReceive('fetchLicenceData')
            ->andReturn(
                [
                    'id' => 7,
                    'niFlag'=> true,
                    'goodsOrPsv' => ['id'=>'lcat_gv'],
                    'trafficArea' => ['id' => 'B']
                ]
            )
            ->once()
            ->getMock();

        $sut = new PiVenue();
        $sut->setLicenceService($mockLicenceService);

        $sut->setData('PiVenue', $input);

        $this->assertEquals($expected, $sut->fetchListOptions(''));
    }

    public function provideFetchListOptions()
    {
        return [
            [$this->getSingleSource(), $this->getSingleExpected()],
            [false, []]
        ];
    }

    public function testFetchListData()
    {
        $results = ['results' => 'results'];
        $params = [
            'trafficArea' => 'B',
            'limit' => 1000,
            'page' => 1
        ];
        $dto = Qry::create($params);
        $mockTransferAnnotationBuilder = m::mock()
            ->shouldReceive('createQuery')->once()->andReturnUsing(
                function ($dto) use ($params) {
                    $this->assertEquals($params['limit'], $dto->getLimit());
                    $this->assertEquals($params['page'], $dto->getPage());
                    $this->assertEquals($params['trafficArea'], $dto->getTrafficArea());
                    return 'query';
                }
            )
            ->once()
            ->getMock();

        $mockResponse = m::mock()
            ->shouldReceive('isOk')
            ->andReturn(true)
            ->once()
            ->shouldReceive('getResult')
            ->andReturn($results)
            ->twice()
            ->getMock();

        $sut = new PiVenue();
        $this->mockHandleQuery($sut, $mockTransferAnnotationBuilder, $mockResponse);

        $this->assertEquals($results['results'], $sut->fetchListData($params));
    }

    public function testFetchLicenceDataWithException()
    {
        $this->setExpectedException(UnexpectedResponseException::class);
        $mockTransferAnnotationBuilder = m::mock()
            ->shouldReceive('createQuery')->once()->andReturn('query')->getMock();

        $mockResponse = m::mock()
            ->shouldReceive('isOk')
            ->andReturn(false)
            ->once()
            ->getMock();
        $sut = new PiVenue();
        $this->mockHandleQuery($sut, $mockTransferAnnotationBuilder, $mockResponse);

        $sut->fetchListData(['trafficArea' => 'B']);
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
