<?php

namespace CommonTest\Common\Service\Data;

use Common\Exception\DataServiceException;
use Common\Service\Data\RefData;
use Mockery as m;
use Dvsa\Olcs\Transfer\Query\RefData\RefDataList as Qry;

/**
 * Class RefDataTest
 * @package CommonTest\Service
 */
class RefDataTest extends RefDataTestCase
{
    /** @var RefData */
    private $sut;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sut = new RefData($this->refDataServices);
    }

    public function testFormatData()
    {
        $source = $this->getSingleSource();
        $expected = $this->getSingleExpected();

        $this->assertEquals($expected, $this->sut->formatData($source));
    }

    public function testFormatDataForGroups()
    {
        $source = $this->getGroupSource();
        $expected = $this->getGroupExpected();

        $this->assertEquals($expected, $this->sut->formatDataForGroups($source));
    }

    /**
     * @dataProvider provideFetchListOptions
     */
    public function testFetchListOptions($source, $expected, $useGroups)
    {
        $this->sut->setData('test', $source);

        $this->assertEquals($expected, $this->sut->fetchListOptions('test', $useGroups));
    }

    public function provideFetchListOptions()
    {
        return [
            [false, [], false],
            [$this->getSingleSource(), $this->getSingleExpected(), false],
            [$this->getGroupSource(), $this->getGroupExpected(), true],
        ];
    }

    /**
     * @return array
     */
    protected function getGroupExpected()
    {
        $expected = array (
            'parent' => array (
                'label' => 'Parent',
                'options' => array (),
            ),
            'p1' => array (
                'options' => array (
                  'val-1' => 'Value 1',
                ),
            ),
            'p2' => array (
                'options' => array (
                  'val-2' => 'Value 2',
                ),
            ),
            'p3' => array (
                'options' => array (
                  'val-3' => 'Value 3',
                ),
            ),
        );
        return $expected;
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
            ['id' => 'val-1', 'description' => 'Value 1'],
            ['id' => 'val-2', 'description' => 'Value 2'],
            ['id' => 'val-3', 'description' => 'Value 3'],
        ];
        return $source;
    }

    protected function getGroupSource()
    {
        $source = [
            ['id' => 'parent', 'description' => 'Parent'],
            ['id' => 'val-1', 'description' => 'Value 1', 'parent' => ['id'=>'p1', 'description'=>'d1']],
            ['id' => 'val-2', 'description' => 'Value 2', 'parent' => ['id'=>'p2', 'description'=>'d2']],
            ['id' => 'val-3', 'description' => 'Value 3', 'parent' => ['id'=>'p3', 'description'=>'d3']],
        ];
        return $source;
    }

    public function testFetchListData()
    {
        $results = ['results' => 'results'];
        $params = [
            'refDataCategory' => 'cat',
            'language' => 'en'
        ];

        $dto = Qry::create($params);

        $this->transferAnnotationBuilder->shouldReceive('createQuery')
            ->with(m::type(Qry::class))
            ->once()
            ->andReturnUsing(
                function ($dto) use ($params) {
                    $this->assertEquals($params['refDataCategory'], $dto->getRefDataCategory());
                    $this->assertEquals($params['language'], $dto->getLanguage());
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

        $this->languagePreferenceService->shouldReceive('getPreference')
            ->once()
            ->andReturn('en');

        $this->assertEquals($results['results'], $this->sut->fetchListData('cat'));
    }

    public function testFetchListDataWithException()
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

        $this->languagePreferenceService->shouldReceive('getPreference')
            ->once()
            ->andReturn('en');

        $this->sut->fetchListData('cat');
    }
}
