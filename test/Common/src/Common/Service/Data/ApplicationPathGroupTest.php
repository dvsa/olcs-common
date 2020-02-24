<?php

namespace CommonTest\Service\Data;

use Common\Exception\DataServiceException;
use Common\Service\Data\ApplicationPathGroup;
use Mockery as m;

/**
 * Class Application Path Group Test
 * @package CommonTest\Service
 */
class ApplicationPathGroupTest extends AbstractDataServiceTestCase
{
    public function testFormatData()
    {
        $source = $this->getSingleSource();
        $expected = $this->getSingleExpected();

        $sut = new ApplicationPathGroup();

        $this->assertEquals($expected, $sut->formatData($source));
    }

    /**
     * @dataProvider provideFetchListOptions
     * @param $input
     * @param $expected
     */
    public function testFetchListOptions($input, $expected)
    {
        $sut = new ApplicationPathGroup();
        $sut->setData('ApplicationPathGroup', $input);

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
        $mockTransferAnnotationBuilder = m::mock()
            ->shouldReceive('createQuery')->once()
            ->andReturn('query')
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

        $sut = new ApplicationPathGroup();
        $this->mockHandleQuery($sut, $mockTransferAnnotationBuilder, $mockResponse);

        $this->assertEquals($results['results'], $sut->fetchListData([]));
    }

    public function testFetchListDataWithException()
    {
        $this->expectException(DataServiceException::class);
        $mockTransferAnnotationBuilder = m::mock()
            ->shouldReceive('createQuery')->once()->andReturn('query')->getMock();

        $mockResponse = m::mock()
            ->shouldReceive('isOk')
            ->andReturn(false)
            ->once()
            ->getMock();
        $sut = new ApplicationPathGroup();
        $this->mockHandleQuery($sut, $mockTransferAnnotationBuilder, $mockResponse);

        $sut->fetchListData([]);
    }

    /**
     * @return array
     */
    protected function getSingleExpected()
    {
        $expected = [
            '1' => 'APG1',
            '2' => 'APG2',
            '3' => 'APG3',
        ];
        return $expected;
    }

    /**
     * @return array
     */
    protected function getSingleSource()
    {
        $source = [
            ['id' => '1', 'name' => 'APG1'],
            ['id' => '2', 'name' => 'APG2'],
            ['id' => '3', 'name' => 'APG3'],
        ];
        return $source;
    }
}
