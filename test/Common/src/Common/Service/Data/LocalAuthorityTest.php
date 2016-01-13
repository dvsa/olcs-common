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

    /**
     * @dataProvider provideFetchListData
     * @param $data
     * @param $expected
     */
    public function testFetchListData($data, $expected)
    {
        $results = ['results' => 'results'];
        $params = [
            'limit' => 1000,
            'page' => 1
        ];
        $dto = Qry::create($params);
        $mockTransferAnnotationBuilder = m::mock()
            ->shouldReceive('createQuery')->once()->andReturnUsing(
                function ($dto) use ($params) {
                    $this->assertEquals($params['limit'], $dto->getLimit());
                    $this->assertEquals($params['page'], $dto->getPage());
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
            ->once()
            ->getMock();

        $sut = new LocalAuthority();
        $this->mockHandleQuery($sut, $mockTransferAnnotationBuilder, $mockResponse);

        $this->assertEquals($results['results'], $sut->fetchListData());
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
            'val-1' => 'Value 1',
            'val-2' => 'Value 2',
            'val-3' => 'Value 3',
        ];
        return $expected;
    }

    /**
     * @return array
     */
    protected function getGroupsExpected()
    {
        $expected = [
            'B' => [
                'label' => 'Bee',
                'options' => [
                    'val-1' => 'Value 1',
                ],
            ],
            'A' => [
                'label' => 'Aye',
                'options' => [
                    'val-2' => 'Value 2',
                ],
            ],
            'C' => [
                'label' => 'Cee',
                'options' => [
                    'val-3' => 'Value 3',
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
            ['id' => 'val-1', 'description' => 'Value 1', 'txcName' => 'B', 'trafficArea' => ['name' => 'Bee']],
            ['id' => 'val-2', 'description' => 'Value 2', 'txcName' => 'A', 'trafficArea' => ['name' => 'Aye']],
            ['id' => 'val-3', 'description' => 'Value 3', 'txcName' => 'C', 'trafficArea' => ['name' => 'Cee']]
        ];
        return $source;
    }
}
