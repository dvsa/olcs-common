<?php

namespace CommonTest\Controller\Lva;

use Mockery as m;
use CommonTest\Bootstrap;
use Common\Service\Entity\VehicleEntityService;

/**
 * Test Abstract Vehicles PSV Controller
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class AbstractVehiclesPsvControllerTest extends AbstractLvaControllerTestCase
{
    protected $adapter;

    public function setUp()
    {
        parent::setUp();

        $this->mockController('\Common\Controller\Lva\AbstractVehiclesPsvController');

        $this->adapter = m::mock('\Common\Controller\Lva\Interfaces\AdapterInterface');
        $this->sut->setAdapter($this->adapter);

        $this->mockService('Script', 'loadFiles')->with(['lva-crud', 'vehicle-psv']);
    }

    /**
     * Get index
     *
     * @group abstractVehiclePsvController
     */
    public function testGetIndexAction()
    {
        $mockValidator = $this->mockService('oneRowInTablesRequired', 'setRows')
            ->with([0, 0, 0])
            ->shouldReceive('setCrud')
            ->with(false)
            ->getMock();

        $form = $this->createMockForm('Lva\PsvVehicles');

        $form->shouldReceive('setData')
            ->with(
                [
                    'data' => [
                        'version' => 1,
                        'hasEnteredReg' => 'N'
                    ]
                ]
            )
            ->andReturn($form)
            ->shouldReceive('has')
            ->with('small')
            ->andReturn(false)
            ->shouldReceive('has')
            ->with('medium')
            ->andReturn(false)
            ->shouldReceive('has')
            ->with('large')
            ->andReturn(false)
            ->shouldReceive('get')
            ->with('small')
            ->andReturn(
                m::mock()
                ->shouldReceive('get')
                ->with('rows')
                ->andReturn(
                    m::mock()
                    ->shouldReceive('getValue')
                    ->andReturn(0)
                    ->getMock()
                )
                ->getMock()
            )
            ->shouldReceive('get')
            ->with('medium')
            ->andReturn(
                m::mock()
                ->shouldReceive('get')
                ->with('rows')
                ->andReturn(
                    m::mock()
                    ->shouldReceive('getValue')
                    ->andReturn(0)
                    ->getMock()
                )
                ->getMock()
            )
            ->shouldReceive('get')
            ->with('large')
            ->andReturn(
                m::mock()
                ->shouldReceive('get')
                ->with('rows')
                ->andReturn(
                    m::mock()
                    ->shouldReceive('getValue')
                    ->andReturn(0)
                    ->getMock()
                )
                ->getMock()
            )
            ->shouldReceive('getInputFilter')
            ->andReturn(
                m::mock()
                ->shouldReceive('get')
                ->with('data')
                ->andReturn(
                    m::mock()
                    ->shouldReceive('get')
                    ->with('hasEnteredReg')
                    ->andReturn(
                        m::mock()
                        ->shouldReceive('getValidatorChain')
                        ->andReturn(
                            m::mock()
                            ->shouldReceive('attach')
                            ->with($mockValidator)
                            ->getMock()
                        )
                        ->getMock()
                    )
                    ->getMock()
                )
                ->getMock()
            )
            ->shouldReceive('remove');

        $this->getMockFormHelper()
            ->shouldReceive('remove')
            ->with($form, 'data->notice');

        $this->sut->shouldReceive('getIdentifier')
            ->andReturn(321)
            ->shouldReceive('getLvaEntityService')
            ->andReturn(
                m::mock()
                ->shouldReceive('getDataForVehiclesPsv')
                ->with(321)
                ->andReturn(
                    [
                        'version' => 1,
                        'hasEnteredReg' => 'N'
                    ]
                )
                ->getMock()
            )
            ->shouldReceive('getTypeOfLicenceData')
            ->andReturn(
                [
                    'version' => 1,
                    'niFlag' => 'x',
                    'goodsOrPsv' => 'y',
                    'licenceType' => 'z'
                ]
            );

        $this->mockRender();

        $this->sut->indexAction();

        $this->assertEquals('vehicles_psv', $this->view);
    }

    public function testBasicSmallAddAction()
    {
        $form = $this->createMockForm('Lva\PsvVehiclesVehicle');

        $specifiedDate = m::mock();
        $removalDate = m::mock();
        $mockEntityService = m::mock();
        $mockLicenceEntity = m::mock();
        $this->sm->setService('Entity\Licence', $mockLicenceEntity);

        $form->shouldReceive('setData')
            ->andReturn($form)
            ->shouldReceive('get')
            ->with('licence-vehicle')
            ->andReturn(
                m::mock()
                ->shouldReceive('get')
                ->with('discNo')
                ->andReturn(
                    m::mock()
                    ->shouldReceive('setAttribute')
                    ->with('disabled', 'disabled')
                    ->getMock()
                )
                ->shouldReceive('get')
                ->with('specifiedDate')
                ->andReturn($specifiedDate)
                ->shouldReceive('get')
                ->with('removalDate')
                ->andReturn($removalDate)
                ->shouldReceive('has')
                ->with('receivedDate')
                ->andReturn(false)
                ->getMock()
            );

        $this->getMockFormHelper()
            ->shouldReceive('disableDateElement')
            ->with($specifiedDate)
            ->shouldReceive('disableDateElement')
            ->with($removalDate);

        $this->shouldRemoveElements(
            $form,
            [
                'data->isNovelty',
                'data->makeModel',
                'licence-vehicle->discNo'
            ]
        );

        $this->mockRender();

        $this->mockEntity('LicenceVehicle', 'getVehiclePsv')
            ->with(50)
            ->andReturn([]);

        $this->sut->shouldReceive('params')
            ->with('child_id')
            ->andReturn(50)
            ->shouldReceive('params')
            ->with('action')
            ->andReturn('small-add')
            ->shouldReceive('getLvaEntityService')
            ->andReturn($mockEntityService)
            ->shouldReceive('getIdentifier')
            ->andReturn(123)
            ->shouldReceive('getLicenceId')
            ->andReturn(321);

        $mockEntityService->shouldReceive('getTotalVehicleAuthorisation')
            ->with(123, '')
            ->andReturn(10);

        $mockLicenceEntity->shouldReceive('getVehiclesPsvTotal')
            ->with(321, '')
            ->andReturn(8);

        $this->sut->smallAddAction();

        $this->assertEquals('add_vehicle', $this->view);
    }

    public function testSmallAddAction()
    {
        $this->sut->shouldReceive('addOrEdit')
            ->with('add', 'small')
            ->andReturn('RETURN');

        $this->assertEquals('RETURN', $this->sut->smallAddAction());
    }

    public function testSmallEditAction()
    {
        $this->sut->shouldReceive('addOrEdit')
            ->with('edit', 'small')
            ->andReturn('RETURN');

        $this->assertEquals('RETURN', $this->sut->smallEditAction());
    }

    public function testSmallDeleteAction()
    {
        $this->sut->shouldReceive('deleteAction')
            ->andReturn('RETURN');

        $this->assertEquals('RETURN', $this->sut->smallDeleteAction());
    }

    public function testMediumAddAction()
    {
        $this->sut->shouldReceive('addOrEdit')
            ->with('add', 'medium')
            ->andReturn('RETURN');

        $this->assertEquals('RETURN', $this->sut->mediumAddAction());
    }

    public function testMediumEditAction()
    {
        $this->sut->shouldReceive('addOrEdit')
            ->with('edit', 'medium')
            ->andReturn('RETURN');

        $this->assertEquals('RETURN', $this->sut->mediumEditAction());
    }

    public function testMediumDeleteAction()
    {
        $this->sut->shouldReceive('deleteAction')
            ->andReturn('RETURN');

        $this->assertEquals('RETURN', $this->sut->mediumDeleteAction());
    }

    public function testLargeAddAction()
    {
        $this->sut->shouldReceive('addOrEdit')
            ->with('add', 'large')
            ->andReturn('RETURN');

        $this->assertEquals('RETURN', $this->sut->largeAddAction());
    }

    public function testLargeEditAction()
    {
        $this->sut->shouldReceive('addOrEdit')
            ->with('edit', 'large')
            ->andReturn('RETURN');

        $this->assertEquals('RETURN', $this->sut->largeEditAction());
    }

    public function testLargeDeleteAction()
    {
        $this->sut->shouldReceive('deleteAction')
            ->andReturn('RETURN');

        $this->assertEquals('RETURN', $this->sut->largeDeleteAction());
    }

    /**
     * Get index
     *
     * @group abstractVehiclePsvController
     */
    public function testGetIndexActionWithTables()
    {
        $stubbedRawTableData = [
            [
                'foo' => 'bar',
                'vehicle' => [
                    'cake' => 'bar',
                    'id' => 1,
                    'psvType' => [
                        'id' => VehicleEntityService::PSV_TYPE_SMALL
                    ]
                ]
            ],
            [
                'foo' => 'bar',
                'vehicle' => [
                    'cake' => 'bar',
                    'id' => 2,
                    'psvType' => [
                        'id' => VehicleEntityService::PSV_TYPE_MEDIUM
                    ]
                ]
            ],
            [
                'foo' => 'bar',
                'vehicle' => [
                    'cake' => 'bar',
                    'id' => 3,
                    'psvType' => [
                        'id' => VehicleEntityService::PSV_TYPE_LARGE
                    ]
                ]
            ]
        ];
        $expectedSmallTableData = [
            [
                'foo' => 'bar',
                'cake' => 'bar',
                'psvType' => [
                    'id' => VehicleEntityService::PSV_TYPE_SMALL
                ]
            ]
        ];
        $expectedMediumTableData = [
            [
                'foo' => 'bar',
                'cake' => 'bar',
                'psvType' => [
                    'id' => VehicleEntityService::PSV_TYPE_MEDIUM
                ]
            ]
        ];
        $expectedLargeTableData = [
            [
                'foo' => 'bar',
                'cake' => 'bar',
                'psvType' => [
                    'id' => VehicleEntityService::PSV_TYPE_LARGE
                ]
            ]
        ];

        $mockSmall = m::mock('\Zend\Form\Fieldset')
            ->shouldReceive('get')
            ->with('rows')
            ->andReturn(
                m::mock()
                ->shouldReceive('getValue')
                ->andReturn(0)
                ->getMock()
            )
            ->getMock();

        $mockMedium = m::mock('\Zend\Form\Fieldset')
            ->shouldReceive('get')
            ->with('rows')
            ->andReturn(
                m::mock()
                ->shouldReceive('getValue')
                ->andReturn(0)
                ->getMock()
            )
            ->getMock();

        $mockLarge = m::mock('\Zend\Form\Fieldset')
            ->shouldReceive('get')
            ->with('rows')
            ->andReturn(
                m::mock()
                ->shouldReceive('getValue')
                ->andReturn(0)
                ->getMock()
            )
            ->getMock();

        $mockValidator = $this->mockService('oneRowInTablesRequired', 'setRows')
            ->with([0, 0, 0])
            ->shouldReceive('setCrud')
            ->with(false)
            ->getMock();

        $form = $this->createMockForm('Lva\PsvVehicles');

        $form->shouldReceive('setData')
            ->with(
                [
                    'data' => [
                        'version' => 1,
                        'hasEnteredReg' => 'N'
                    ]
                ]
            )
            ->andReturnSelf()
            ->shouldReceive('has')
            ->with('small')
            ->andReturn(true)
            ->shouldReceive('has')
            ->with('medium')
            ->andReturn(true)
            ->shouldReceive('has')
            ->with('large')
            ->andReturn(true)
            ->shouldReceive('get')
            ->with('small')
            ->andReturn($mockSmall)
            ->shouldReceive('get')
            ->with('medium')
            ->andReturn($mockMedium)
            ->shouldReceive('get')
            ->with('large')
            ->andReturn($mockLarge)
            ->shouldReceive('getInputFilter')
            ->andReturn(
                m::mock()
                ->shouldReceive('get')
                ->with('data')
                ->andReturn(
                    m::mock()
                    ->shouldReceive('get')
                    ->with('hasEnteredReg')
                    ->andReturn(
                        m::mock()
                        ->shouldReceive('getValidatorChain')
                        ->andReturn(
                            m::mock()
                            ->shouldReceive('attach')
                            ->with($mockValidator)
                            ->getMock()
                        )
                        ->getMock()
                    )
                    ->getMock()
                )
                ->getMock()
            )
            ->shouldReceive('remove');

        $this->getMockFormHelper()
            ->shouldReceive('remove')
            ->with($form, 'data->notice');

        $this->sut->shouldReceive('getIdentifier')
            ->andReturn(321)
            ->shouldReceive('getLvaEntityService')
            ->andReturn(
                m::mock()
                ->shouldReceive('getDataForVehiclesPsv')
                ->with(321)
                ->andReturn(
                    [
                        'version' => 1,
                        'hasEnteredReg' => 'N'
                    ]
                )
                ->getMock()
            )
            ->shouldReceive('getTypeOfLicenceData')
            ->andReturn(
                [
                    'version' => 1,
                    'niFlag' => 'x',
                    'goodsOrPsv' => 'y',
                    'licenceType' => 'z'
                ]
            );

        $this->adapter->shouldReceive('getVehiclesData')
            ->once()
            ->with(321)
            ->andReturn($stubbedRawTableData);

        $mockTable = m::mock('\Common\Service\Table\TableBuilder');
        $mockTableBuilder = m::mock('\Common\Service\Table\TableBuilder');
        $this->sm->setService('Table', $mockTableBuilder);

        $mockTableBuilder->shouldReceive('prepareTable')
            ->with('lva-psv-vehicles-small', $expectedSmallTableData)
            ->andReturn($mockTable)
            ->shouldReceive('prepareTable')
            ->with('lva-psv-vehicles-medium', $expectedMediumTableData)
            ->andReturn($mockTable)
            ->shouldReceive('prepareTable')
            ->with('lva-psv-vehicles-large', $expectedLargeTableData)
            ->andReturn($mockTable);

        $this->getMockFormHelper()->shouldReceive('populateFormTable')
            ->with($mockSmall, $mockTable, 'small')
            ->shouldReceive('populateFormTable')
            ->with($mockMedium, $mockTable, 'medium')
            ->shouldReceive('populateFormTable')
            ->with($mockLarge, $mockTable, 'large');

        $this->sut->shouldReceive('showVehicle')
            ->andReturn(true);

        $this->mockRender();

        $this->sut->indexAction();

        $this->assertEquals('vehicles_psv', $this->view);
    }

    /**
     * Get index
     *
     * @group abstractVehiclePsvController
     */
    public function testGetIndexActionWithTablesWhenNotShowingVehicles()
    {
        $stubbedRawTableData = [
            [
                'foo' => 'bar',
                'vehicle' => [
                    'cake' => 'bar',
                    'id' => 1,
                    'psvType' => [
                        'id' => VehicleEntityService::PSV_TYPE_SMALL
                    ]
                ]
            ],
            [
                'foo' => 'bar',
                'vehicle' => [
                    'cake' => 'bar',
                    'id' => 2,
                    'psvType' => [
                        'id' => VehicleEntityService::PSV_TYPE_MEDIUM
                    ]
                ]
            ],
            [
                'foo' => 'bar',
                'vehicle' => [
                    'cake' => 'bar',
                    'id' => 3,
                    'psvType' => [
                        'id' => VehicleEntityService::PSV_TYPE_LARGE
                    ]
                ]
            ]
        ];
        $expectedSmallTableData = [];
        $expectedMediumTableData = [];
        $expectedLargeTableData = [];

        $mockSmall = m::mock('\Zend\Form\Fieldset')
            ->shouldReceive('get')
            ->with('rows')
            ->andReturn(
                m::mock()
                ->shouldReceive('getValue')
                ->andReturn(0)
                ->getMock()
            )
            ->getMock();

        $mockMedium = m::mock('\Zend\Form\Fieldset')
            ->shouldReceive('get')
            ->with('rows')
            ->andReturn(
                m::mock()
                ->shouldReceive('getValue')
                ->andReturn(0)
                ->getMock()
            )
            ->getMock();

        $mockLarge = m::mock('\Zend\Form\Fieldset')
            ->shouldReceive('get')
            ->with('rows')
            ->andReturn(
                m::mock()
                ->shouldReceive('getValue')
                ->andReturn(0)
                ->getMock()
            )
            ->getMock();

        $mockValidator = $this->mockService('oneRowInTablesRequired', 'setRows')
            ->with([0, 0, 0])
            ->shouldReceive('setCrud')
            ->with(false)
            ->getMock();

        $form = $this->createMockForm('Lva\PsvVehicles');

        $form->shouldReceive('setData')
            ->with(
                [
                    'data' => [
                        'version' => 1,
                        'hasEnteredReg' => 'N'
                    ]
                ]
            )
            ->andReturnSelf()
            ->shouldReceive('has')
            ->with('small')
            ->andReturn(true)
            ->shouldReceive('has')
            ->with('medium')
            ->andReturn(true)
            ->shouldReceive('has')
            ->with('large')
            ->andReturn(true)
            ->shouldReceive('get')
            ->with('small')
            ->andReturn($mockSmall)
            ->shouldReceive('get')
            ->with('medium')
            ->andReturn($mockMedium)
            ->shouldReceive('get')
            ->with('large')
            ->andReturn($mockLarge)
            ->shouldReceive('getInputFilter')
            ->andReturn(
                m::mock()
                ->shouldReceive('get')
                ->with('data')
                ->andReturn(
                    m::mock()
                    ->shouldReceive('get')
                    ->with('hasEnteredReg')
                    ->andReturn(
                        m::mock()
                        ->shouldReceive('getValidatorChain')
                        ->andReturn(
                            m::mock()
                            ->shouldReceive('attach')
                            ->with($mockValidator)
                            ->getMock()
                        )
                        ->getMock()
                    )
                    ->getMock()
                )
                ->getMock()
            )
            ->shouldReceive('remove');

        $this->getMockFormHelper()
            ->shouldReceive('remove')
            ->with($form, 'data->notice');

        $this->sut->shouldReceive('getIdentifier')
            ->andReturn(321)
            ->shouldReceive('getLvaEntityService')
            ->andReturn(
                m::mock()
                ->shouldReceive('getDataForVehiclesPsv')
                ->with(321)
                ->andReturn(
                    [
                        'version' => 1,
                        'hasEnteredReg' => 'N'
                    ]
                )
                ->getMock()
            )
            ->shouldReceive('getTypeOfLicenceData')
            ->andReturn(
                [
                    'version' => 1,
                    'niFlag' => 'x',
                    'goodsOrPsv' => 'y',
                    'licenceType' => 'z'
                ]
            );

        $this->adapter->shouldReceive('getVehiclesData')
            ->once()
            ->with(321)
            ->andReturn($stubbedRawTableData);

        $mockTable = m::mock('\Common\Service\Table\TableBuilder');
        $mockTableBuilder = m::mock('\Common\Service\Table\TableBuilder');
        $this->sm->setService('Table', $mockTableBuilder);

        $mockTableBuilder->shouldReceive('prepareTable')
            ->with('lva-psv-vehicles-small', $expectedSmallTableData)
            ->andReturn($mockTable)
            ->shouldReceive('prepareTable')
            ->with('lva-psv-vehicles-medium', $expectedMediumTableData)
            ->andReturn($mockTable)
            ->shouldReceive('prepareTable')
            ->with('lva-psv-vehicles-large', $expectedLargeTableData)
            ->andReturn($mockTable);

        $this->getMockFormHelper()->shouldReceive('populateFormTable')
            ->with($mockSmall, $mockTable, 'small')
            ->shouldReceive('populateFormTable')
            ->with($mockMedium, $mockTable, 'medium')
            ->shouldReceive('populateFormTable')
            ->with($mockLarge, $mockTable, 'large');

        $this->sut->shouldReceive('showVehicle')
            ->andReturn(false);

        $this->mockRender();

        $this->sut->indexAction();

        $this->assertEquals('vehicles_psv', $this->view);
    }

    /**
     * Test remove vehicle fields
     *
     * @group vehcileFormAdapterPsv
     */
    public function testRemoveVehicleFields()
    {
        $mockRequest = m::mock()
            ->shouldReceive('isPost')
            ->andReturn(false)
            ->getMock();

        $mockForm = $this->createMockForm('Lva\PsvVehiclesVehicle')
            ->shouldReceive('setData')
            ->with([])
            ->andReturnSelf()
            ->shouldReceive('isValid')
            ->andReturn(false)
            ->getMock();

        $mockVehicleFormAdapter = m::mock()
            ->shouldReceive('alterForm')
            ->with($mockForm)
            ->andReturn($mockForm)
            ->getMock();

        $this->sm->setService('VehicleFormAdapter', $mockVehicleFormAdapter);

        $this->sut
            ->shouldReceive('getRequest')
            ->andReturn($mockRequest)
            ->shouldReceive('params')
            ->with('child_id')
            ->andReturn(1)
            ->shouldReceive('getVehicleFormData')
            ->with(1)
            ->andReturn([])
            ->shouldReceive('formatVehicleDataForForm')
            ->with([], 'small')
            ->andReturn([])
            ->shouldReceive('alterVehicleForm')
            ->with($mockForm, 'add')
            ->andReturn($mockForm)
            ->shouldReceive('setDefaultDates')
            ->andReturn($mockForm)
            ->shouldReceive('render')
            ->andReturn('view');

        $this->assertEquals('view', $this->sut->smallAddAction());
    }
}
