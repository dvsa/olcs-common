<?php

namespace CommonTest\Service\Data;

use Common\Exception\DataServiceException;
use Common\Service\Data\LocalAuthority;
use Mockery as m;
use Dvsa\Olcs\Transfer\Query\LocalAuthority\LocalAuthorityList as Qry;

/**
 * Class LocalAuthority Test
 * @package CommonTest\Service
 */
class LocalAuthorityTest extends AbstractDataServiceTestCase
{
    /** @var LocalAuthority */
    private $sut;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sut = new LocalAuthority($this->abstractDataServiceServices);
    }

    public function testFormatData()
    {
        $source = $this->getSingleSource();
        $expected = $this->getSingleExpected();

        $this->assertEquals($expected, $this->sut->formatData($source));
    }

    public function testFormatDataForGroups()
    {
        $source = $this->getSingleSource();
        $expected = $this->getGroupsExpected();

        $this->assertEquals($expected, $this->sut->formatDataForGroups($source));
    }

    /**
     * @dataProvider provideFetchListOptions
     * @param $input
     * @param $expected
     * @param $useGroups
     */
    public function testFetchListOptions($input, $expected, $useGroups)
    {
        $this->sut->setData('LocalAuthority', $input);

        $this->assertEquals($expected, $this->sut->fetchListOptions('', $useGroups));
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

        $this->transferAnnotationBuilder->shouldReceive('createQuery')
            ->with(m::type(Qry::class))
            ->once()
            ->andReturn($this->query);

        $mockResponse = m::mock()
            ->shouldReceive('isOk')
            ->andReturn(true)
            ->once()
            ->shouldReceive('getResult')
            ->andReturn($results)
            ->once()
            ->getMock();

        $this->mockHandleQuery($mockResponse);

        $this->assertEquals($results['results'], $this->sut->fetchListData());
        $this->assertEquals($results['results'], $this->sut->fetchListData()); //ensure data is cached
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

        $this->sut->fetchListData();
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
