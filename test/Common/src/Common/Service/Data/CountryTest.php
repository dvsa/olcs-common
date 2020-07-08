<?php

namespace CommonTest\Service\Data;

use Common\Exception\DataServiceException;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Service\Data\Country;
use Mockery as m;
use Dvsa\Olcs\Transfer\Query\ContactDetail\CountryList as Qry;

/**
 * Class Country Test
 * @package CommonTest\Service
 */
class CountryTest extends AbstractDataServiceTestCase
{
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
    public function testFetchListOptions($input, $category, $expected)
    {
        $sut = new Country();
        $sut->setData('Country', $input);

        $this->assertEquals($expected, $sut->fetchListOptions($category));
    }

    public function provideFetchListOptions()
    {
        return [
            [$this->getSingleSource(), '', $this->getSingleExpected()],
            [false, '', []],
            [
                $this->getSingleSource(),
                'isMemberState',
                [
                    'val-1' => 'Value 1',
                    'val-2' => 'Value 2',
                    'val-3' => 'Value 3',
                ],
            ],
            [
                $this->getSingleSource(),
                'ecmtConstraint',
                [
                    'val-2' => 'Value 2',
                    'val-5' => 'Value 5',
                ],
            ],
            [
                $this->getSingleSource(),
                'isPermitState',
                [
                    'val-3' => 'Value 3',
                    'val-6' => 'Value 6',
                ],
            ],
        ];
    }

    public function testFetchListData()
    {
        $results = ['results' => 'results'];
        $params = [
            'sort' => 'countryDesc',
            'order' => 'ASC',
        ];
        $dto = Qry::create($params);
        $mockTransferAnnotationBuilder = m::mock()
            ->shouldReceive('createQuery')->once()->andReturnUsing(
                function ($dto) use ($params) {
                    $this->assertEquals($params['sort'], $dto->getSort());
                    $this->assertEquals($params['order'], $dto->getOrder());
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

        $sut = new Country();
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
        $sut = new Country();
        $this->mockHandleQuery($sut, $mockTransferAnnotationBuilder, $mockResponse);

        $sut->fetchListData([]);
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
            'val-4' => 'Value 4',
            'val-5' => 'Value 5',
            'val-6' => 'Value 6',
        ];
        return $expected;
    }

    /**
     * @return array
     */
    protected function getSingleSource()
    {
        $source = [
            [
                'id' => 'val-1',
                'countryDesc' => 'Value 1',
                'isMemberState' => 'Y',
                'constraints' => [],
                'isPermitState' => false,
            ],
            [
                'id' => 'val-2',
                'countryDesc' => 'Value 2',
                'isMemberState' => 'Y',
                'constraints' => ['A'],
                'isPermitState' => false,
            ],
            [
                'id' => 'val-3',
                'countryDesc' => 'Value 3',
                'isMemberState' => 'Y',
                'constraints' => [],
                'isPermitState' => true,
            ],
            [
                'id' => 'val-4',
                'countryDesc' => 'Value 4',
                'isMemberState' => 'N',
                'constraints' => [],
                'isPermitState' => false,
            ],
            [
                'id' => 'val-5',
                'countryDesc' => 'Value 5',
                'isMemberState' => 'N',
                'constraints' => ['A'],
                'isPermitState' => false,
            ],
            [
                'id' => 'val-6',
                'countryDesc' => 'Value 6',
                'isMemberState' => 'N',
                'constraints' => [],
                'isPermitState' => true,
            ],
        ];
        return $source;
    }
}
