<?php

namespace CommonTest\Service\Data;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Service\Data\Country;
use Mockery as m;

/**
 * Class Country Test
 * @package CommonTest\Service
 */
class CountryTest extends MockeryTestCase
{
    public function testGetServiceName()
    {
        $sut = new Country();
        $this->assertEquals('Country', $sut->getServiceName());
    }

    public function testFormatData()
    {
        $source = $this->getSingleSource();
        $expected = $this->getSingleExpected();

        $sut = new Country();

        $this->assertEquals($expected, $sut->formatData($source));
    }

    /**
     * @dataProvider provideFetchListOptions
     * @param $input
     * @param $expected
     */
    public function testFetchListOptions($input, $expected)
    {
        $sut = new Country();
        $sut->setData('Country', $input);

        $this->assertEquals($expected, $sut->fetchListOptions(''));
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
     * @param $data
     * @param $expected
     */
    public function testFetchListData($data, $expected)
    {
        $mockRestClient = m::mock('Common\Util\RestClient');
        $mockRestClient
            ->shouldReceive('get')
            ->once()
            ->with('', ['limit' => 1000, 'sort' => 'countryDesc'])
            ->andReturn($data);

        $sut = new Country();
        $sut->setRestClient($mockRestClient);

        $this->assertEquals($expected, $sut->fetchListData());
        $sut->fetchListData(); //ensure data is cached
    }

    public function provideFetchListData()
    {
        return [
            [false, false],
            [['Results' => $this->getSingleSource()], $this->getSingleSource()],
            [['some' => 'data'],  false]
        ];
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
            ['id' => 'val-1', 'countryDesc' => 'Value 1'],
            ['id' => 'val-2', 'countryDesc' => 'Value 2'],
            ['id' => 'val-3', 'countryDesc' => 'Value 3'],
        ];
        return $source;
    }
}
