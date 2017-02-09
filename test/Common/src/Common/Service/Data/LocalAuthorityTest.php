<?php

namespace CommonTest\Service\Data;

use Common\Service\Data\LocalAuthority;
use Mockery as m;
use Dvsa\Olcs\Transfer\Query\LocalAuthority\LocalAuthorityList as Qry;
use Common\Service\Entity\Exceptions\UnexpectedResponseException;
use CommonTest\Service\Data\AbstractDataServiceTestCase;

/**
 * Class LocalAuthority Test
 * @package CommonTest\Service
 */
class LocalAuthorityTest extends AbstractDataServiceTestCase
{
    public function testFormatData()
    {
        $source = $this->getSingleSource();
        $expected = $this->getSingleExpected();

        $sut = new LocalAuthority();

        $this->assertEquals($expected, $sut->formatData($source));
    }

    public function testFormatDataForGroups()
    {
        $source = $this->getSingleSource();
        $expected = $this->getGroupsExpected();

        $sut = new LocalAuthority();

        $this->assertEquals($expected, $sut->formatDataForGroups($source));
    }

    /**
     * @dataProvider provideFetchListOptions
     * @param $input
     * @param $expected
     * @param $useGroups
     */
    public function testFetchListOptions($input, $expected, $useGroups)
    {
        $sut = new LocalAuthority();
        $sut->setData('LocalAuthority', $input);

        $this->assertEquals($expected, $sut->fetchListOptions('', $useGroups));
    }

    public function provideFetchListOptions()
    {
        return [
            [$this->getSingleSource(), $this->getSingleExpected(), false],
            [false, [], false],
            [$this->getSingleSource(), $this->getGroupsExpected(), true],
        ];
    }

    public function testFetchListData()
    {
        $results = ['results' => 'results'];
        $mockTransferAnnotationBuilder = m::mock()
            ->shouldReceive('createQuery')->once()->andReturn('query')
            ->once()
            ->getMock();

        $mockResponse = m::mock()
            ->shouldReceive('isOk')
            ->andReturn(true)
            ->once()
            ->shouldReceive('getResult')
            ->andReturn($results)
            ->once()
            ->getMock();

        $sut = new LocalAuthority();
        $this->mockHandleQuery($sut, $mockTransferAnnotationBuilder, $mockResponse);

        $this->assertEquals($results['results'], $sut->fetchListData());
        $this->assertEquals($results['results'], $sut->fetchListData()); //ensure data is cached
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
        $sut = new LocalAuthority();
        $this->mockHandleQuery($sut, $mockTransferAnnotationBuilder, $mockResponse);

        $sut->fetchListData();
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
            '1' => 'A1 Council',
            '2' => 'B Council',
            '3' => 'C Council',
            '4' => 'A2 Council',
        ];
        return $expected;
    }

    /**
     * @return array
     */
    protected function getGroupsExpected()
    {
        $expected = [
            'A' => [
                'label' => 'AAA',
                'options' => [
                    '1' => 'A1 Council',
                    '4' => 'A2 Council',
                ],
            ],
            'B' => [
                'label' => 'BBB',
                'options' => [
                    '2' => 'B Council',
                ],
            ],
            'C' => [
                'label' => 'CCC',
                'options' => [
                    '3' => 'C Council',
                ],
            ]
        ];
        return $expected;
    }

    /**
     * @return array
     */
    protected function getSingleSource()
    {
        $source = [
            ['id' => '1', 'description' => 'A1 Council', 'trafficArea' => ['name' => 'AAA', 'id' => 'A']],
            ['id' => '2', 'description' => 'B Council', 'trafficArea' => ['name' => 'BBB', 'id' => 'B']],
            ['id' => '3', 'description' => 'C Council', 'trafficArea' => ['name' => 'CCC', 'id' => 'C']],
            ['id' => '4', 'description' => 'A2 Council', 'trafficArea' => ['name' => 'AAA', 'id' => 'A']],
        ];
        return $source;
    }
}
