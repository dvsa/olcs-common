<?php

/**
 * Application Vehicles Goods Adapter Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Controller\Lva\Adapters;

use CommonTest\Bootstrap;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Controller\Lva\Adapters\ApplicationVehiclesGoodsAdapter;

/**
 * Application Vehicles Goods Adapter Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationVehiclesGoodsAdapterTest extends MockeryTestCase
{
    protected $sut;
    protected $sm;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();

        $this->sut = new ApplicationVehiclesGoodsAdapter();
        $this->sut->setServiceLocator($this->sm);
    }

    public function testGetVehiclesData()
    {
        $mockLicenceEntity = m::mock();
        $this->sm->setService('Entity\Licence', $mockLicenceEntity);

        $mockLicenceEntity->shouldReceive('getVehiclesDataForApplication')
            ->with(3)
            ->andReturn('RESPONSE');

        $this->assertEquals('RESPONSE', $this->sut->getVehiclesData(3));
    }

    public function testSave()
    {
        $data = [
            'data' => [
                'foo' => 'bar'
            ]
        ];
        $id = 3;

        $mockApplicationEntity = m::mock();
        $this->sm->setService('Entity\Application', $mockApplicationEntity);

        $mockApplicationEntity->shouldReceive('save')
            ->with(['foo' => 'bar', 'id' => 3])
            ->andReturn('RESPONSE');

        $this->assertEquals('RESPONSE', $this->sut->save($data, $id));
    }

    public function testGetFormData()
    {
        $id = 3;
        $stubbedResponse = [
            'foo' => 'bar',
            'version' => 5,
            'hasEnteredReg' => 'N'
        ];
        $expectedData = [
            'data' => [
                'version' => 5,
                'hasEnteredReg' => 'N'
            ]
        ];

        $mockApplicationEntity = m::mock();
        $this->sm->setService('Entity\Application', $mockApplicationEntity);

        $mockApplicationEntity->shouldReceive('getHeaderData')
            ->with($id)
            ->andReturn($stubbedResponse);

        $this->assertEquals($expectedData, $this->sut->getFormData($id));
    }

    public function testGetFormDataWithoutHasEnteredReg()
    {
        $id = 3;
        $stubbedResponse = [
            'foo' => 'bar',
            'version' => 5,
            'hasEnteredReg' => 'ABC'
        ];
        $expectedData = [
            'data' => [
                'version' => 5,
                'hasEnteredReg' => 'Y'
            ]
        ];

        $mockApplicationEntity = m::mock();
        $this->sm->setService('Entity\Application', $mockApplicationEntity);

        $mockApplicationEntity->shouldReceive('getHeaderData')
            ->with($id)
            ->andReturn($stubbedResponse);

        $this->assertEquals($expectedData, $this->sut->getFormData($id));
    }

    public function testShowFilters()
    {
        $this->assertTrue($this->sut->showFilters());
    }

    public function testGetFilterForm()
    {
        $vrmOptions = array_merge(['All' => 'All'], array_combine(range('A', 'Z'), range('A', 'Z')));
        $filterForm = m::mock()
            ->shouldReceive('get')
            ->with('vrm')
            ->andReturn(
                m::mock()
                ->shouldReceive('setValueOptions')
                ->with($vrmOptions)
                ->getMock()
            )
            ->getMock();

        $this->sm->setService(
            'Helper\Form',
            m::mock()
            ->shouldReceive('createForm')
            ->with('Lva\VehicleFilter')
            ->andReturn($filterForm)
            ->getMock()
        );

        $this->assertEquals($filterForm, $this->sut->getFilterForm());
    }

    public function testGetFilters()
    {
        $input = [
            'vrm' => 'foo',
            'specified' => 'bar',
            'includeRemoved' => 'baz',
            'disc' => 'test'
        ];

        $expected = [
            'vrm' => 'foo',
            'specified' => 'bar',
            'includeRemoved' => 'baz',
            'disc' => 'test'
        ];

        $this->assertEquals($expected, $this->sut->getFilters($input));
    }

    public function testGetFiltersWithDefaults()
    {
        $input = [];

        $expected = [
            'vrm' => 'All',
            'specified' => 'A',
            'includeRemoved' => 0,
            'disc' => 'A'
        ];

        $this->assertEquals($expected, $this->sut->getFilters($input));
    }

    /**
     * Test maybeDisableRemovedAndSpecifiedDates method
     */
    public function testMaybeDisableRemovedAndSpecifiedDates()
    {
        $mockForm = m::mock()
            ->shouldReceive('get')
            ->with('licence-vehicle')
            ->andReturn(
                m::mock()
                ->shouldReceive('get')
                ->with('specifiedDate')
                ->andReturn('specifiedDate')
                ->once()
                ->shouldReceive('get')
                ->with('removalDate')
                ->andReturn('removedDate')
                ->once()
                ->getMock()
            )
            ->once()
            ->getMock();

        $mockFormHelper = m::mock()
            ->shouldReceive('disableDateElement')
            ->with('specifiedDate')
            ->once()
            ->shouldReceive('disableDateElement')
            ->with('removedDate')
            ->once()
            ->getMock();

        $this->assertEquals(null, $this->sut->maybeDisableRemovedAndSpecifiedDates($mockForm, $mockFormHelper));
    }

    /**
     * Test maybeFormatRemovedAndSpecifiedDates method
     */
    public function testMaybeFormatRemovedAndSpecifiedDates()
    {
        $this->assertEquals('data', $this->sut->maybeFormatRemovedAndSpecifiedDates('data'));
    }

    /**
     * Test maybeUnsetSpecifiedDate method
     */
    public function testMaybeUnsetSpecifiedDate()
    {
        $this->assertEquals(
            ['licence-vehicle' => []],
            $this->sut->maybeUnsetSpecifiedDate(['licence-vehicle' => ['specifiedDate' => 'date']])
        );
    }

    /**
     * Test maybeRemoveSpecifiedDateEmptyOption method
     */
    public function testMaybeRemoveSpecifiedDateEmptyOption()
    {
        $this->assertEquals('form', $this->sut->maybeRemoveSpecifiedDateEmptyOption('form', 'edit'));
    }
}
