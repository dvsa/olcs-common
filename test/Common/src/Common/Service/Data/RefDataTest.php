<?php

namespace CommonTest\Service\Data;

use Common\Service\Data\RefData;
use Mockery as m;
use Dvsa\Olcs\Transfer\Query\ContactDetail\ContactDetailsList as Qry;
use Common\Service\Entity\Exceptions\UnexpectedResponseException;

/**
 * Class RefDataTest
 * @package CommonTest\Service
 */
class RefDataTest extends AbstractDataServiceTestCase
{
    public function testFormatData()
    {
        $source = $this->getSingleSource();
        $expected = $this->getSingleExpected();

        $sut = new RefData();

        $this->assertEquals($expected, $sut->formatData($source));
    }

    public function testFormatDataForGroups()
    {
        $source = $this->getGroupSource();
        $expected = $this->getGroupExpected();

        $sut = new RefData();

        $this->assertEquals($expected, $sut->formatDataForGroups($source));
    }

    /**
     * @dataProvider provideFetchListOptions
     */
    public function testFetchListOptions($source, $expected, $useGroups)
    {
        $sut = new RefData();
        $sut->setData('test', $source);

        $this->assertEquals($expected, $sut->fetchListOptions('test', $useGroups));
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
        $mockTransferAnnotationBuilder = m::mock()
            ->shouldReceive('createQuery')->once()->andReturnUsing(
                function ($dto) use ($params) {
                    $this->assertEquals($params['refDataCategory'], $dto->getRefDataCategory());
                    $this->assertEquals($params['language'], $dto->getLanguage());
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

        $sut = new RefData();
        $this->mockHandleQuery($sut, $mockTransferAnnotationBuilder, $mockResponse);
        $this->mockServiceLocator
            ->shouldReceive('get')
            ->with('LanguagePreference')
            ->andReturn(
                m::mock()
                    ->shouldReceive('getPreference')
                    ->andReturn('en')
                    ->once()
                    ->getMock()
            )
            ->once()
            ->getMock();

        $this->assertEquals($results['results'], $sut->fetchListData('cat'));
    }

    public function testFetchListDataWithException()
    {
        $this->setExpectedException(UnexpectedResponseException::class);
        $mockTransferAnnotationBuilder = m::mock()
            ->shouldReceive('createQuery')->once()->andReturn('query')->getMock();

        $mockResponse = m::mock()
            ->shouldReceive('isOk')
            ->andReturn(false)
            ->once()
            ->getMock();
        $sut = new RefData();
        $this->mockHandleQuery($sut, $mockTransferAnnotationBuilder, $mockResponse);
        $this->mockServiceLocator
            ->shouldReceive('get')
            ->with('LanguagePreference')
            ->andReturn(
                m::mock()
                    ->shouldReceive('getPreference')
                    ->andReturn('en')
                    ->once()
                    ->getMock()
            )
            ->once()
            ->getMock();

        $sut->fetchListData('cat');
    }
}
