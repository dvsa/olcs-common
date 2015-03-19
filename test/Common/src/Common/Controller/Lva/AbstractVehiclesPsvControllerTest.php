<?php

namespace CommonTest\Controller\Lva;

use Mockery as m;
use CommonTest\Bootstrap;
use Common\Service\Entity\VehicleEntityService;
use Common\Service\Entity\LicenceEntityService;

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

        // stub the mapping between type and psv type that is now in entity service
        $map = [
            'small'  => 'vhl_t_a',
            'medium' => 'vhl_t_b',
            'large'  => 'vhl_t_c',
        ];
        $this->mockEntity('Vehicle', 'getTypeMap')->andReturn($map);
        $this->mockEntity('Vehicle', 'getPsvTypeFromType')->andReturnUsing(
            function($type) use ($map) {
                return isset($map[$type]) ? $map[$type] : null;
            }
        );
    }

    /**
     * Common setup for testGetIndexAction and testIndexActionPostValid
     */
    protected function indexActionSetup(&$form, $id, $entityData)
    {
        $mockValidator = $this->mockService('oneRowInTablesRequired', 'setRows')
            ->with([0, 0, 0])
            ->shouldReceive('setCrud')
            ->with(false)
            ->getMock();

        $form->shouldReceive('has')
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
            ->andReturn($id)
            ->shouldReceive('getLvaEntityService')
            ->andReturn(
                m::mock()
                ->shouldReceive('getDataForVehiclesPsv')
                ->with($id)
                ->andReturn($entityData)
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
    }

    /**
     * Get index
     *
     * @group abstractVehiclePsvController
     */
    public function testGetIndexAction()
    {
        $id = 69;

        $form = $this->createMockForm('Lva\PsvVehicles');

        $entityData = [
            'id' => $id,
            'version' => 1,
            'hasEnteredReg' => 'N',
            'licence' => ['licenceVehicles' => []],
        ];

        $this->indexActionSetup($form, $id, $entityData);

        $form->shouldReceive('setData')
            ->once()
            ->with(
                [
                    'data' => [
                        'version' => 1,
                        'hasEnteredReg' => 'N'
                    ]
                ]
            )
            ->andReturnSelf();

        $this->adapter
            ->shouldReceive('getVehicleCountByPsvType')->andReturn(0)
            ->shouldReceive('warnIfAuthorityExceeded')
                ->with($id, m::any(), false)
                ->once();

        $this->mockRender();

        $this->sut->indexAction();

        $this->assertEquals('vehicles_psv', $this->view);
    }

    public function testIndexActionPostValid()
    {
        $id = 69;

        $form = $this->createMockForm('Lva\PsvVehicles');

        $entityData = [
            'id' => $id,
            'version' => 1,
            'hasEnteredReg' => 'N',
            'licence' => ['licenceVehicles' => []],
        ];

        $this->indexActionSetup($form, $id, $entityData);

        $postData = ['POST'];
        $this->setPost($postData);

        $form->shouldReceive('setData')->once()->with($postData)->andReturnSelf();
        $form->shouldReceive('isValid')->andReturn('true');

        $formData = [
            'data' => [
                'hasEnteredReg' => 'Y'
            ]
        ];
        $form->shouldReceive('getData')->andReturn($formData);
        $this->sut->shouldReceive('save')->with($formData);

        $this->adapter
            ->shouldReceive('getVehicleCountByPsvType')->andReturn(0)
            ->shouldReceive('warnIfAuthorityExceeded')
                ->with($id, m::any(), true)
                ->once();

        $redirect = m::mock();
        $this->sut->shouldReceive('completeSection')->andReturn($redirect);

        $this->assertSame($redirect, $this->sut->indexAction());
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
            ->andReturn(321)
            ->shouldReceive('getAdapter')
            ->andReturn(
                m::mock()
                ->shouldReceive('maybeDisableRemovedAndSpecifiedDates')
                ->with($form, $this->getMockFormHelper())
                ->getMock()
            );

        $mockEntityService->shouldReceive('getTotalVehicleAuthorisation')
            ->with(123, 'Small')
            ->andReturn(10);

        $mockLicenceEntity->shouldReceive('getVehiclesPsvTotal')
            ->with(321, 'vhl_t_a')
            ->andReturn(8);

        $this->sut->smallAddAction();

        $this->assertEquals('add_vehicle', $this->view);
    }

    public function testBasicSmallAddActionDisabledAddAnother()
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
            ->andReturn(321)
            ->shouldReceive('getAdapter')
            ->andReturn(
                m::mock()
                ->shouldReceive('maybeDisableRemovedAndSpecifiedDates')
                ->with($form, $this->getMockFormHelper())
                ->getMock()
            );

        $mockEntityService->shouldReceive('getTotalVehicleAuthorisation')
            ->with(123, 'Small')
            ->andReturn(10);

        $mockLicenceEntity->shouldReceive('getVehiclesPsvTotal')
            ->with(321, 'vhl_t_a')
            ->andReturn(9);

        $form->shouldReceive('get')
            ->with('form-actions')
            ->andReturn(
                m::mock()
                ->shouldReceive('remove')
                ->with('addAnother')
                ->getMock()
            );

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
                        'id' => 69,
                        'version' => 1,
                        'hasEnteredReg' => 'N',
                        'licence' => ['licenceVehicles' => []],
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
            ->andReturn($stubbedRawTableData)
            ->shouldReceive('getVehicleCountByPsvType')
            ->andReturn(1)
            ->shouldReceive('warnIfAuthorityExceeded')
                ->with(321, m::type('array'), false)
                ->once();

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
                        'id' => 69,
                        'version' => 1,
                        'hasEnteredReg' => 'N',
                        'licence' => ['licenceVehicles' => []],
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
            ->andReturn($stubbedRawTableData)
            ->shouldReceive('getVehicleCountByPsvType')
            ->andReturn(1)
            ->shouldReceive('warnIfAuthorityExceeded');

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

    /**
     * Test that tables are not removed when there is no vehicle authority
     * but previously added vehicles
     *
     * @see https://jira.i-env.net/browse/OLCS-7590
     */
    public function testAlterFormKeepsTablesWithVehiclesWhenNoAuthority()
    {
        $mockForm = $this->createMockForm('Lva\PsvVehicles');

        $this->mockRowField($mockForm, 'small', 2);
        $this->mockRowField($mockForm, 'medium', 3);
        $this->mockRowField($mockForm, 'large', 4);

        $mockValidator = $this->mockService('oneRowInTablesRequired', 'setRows')
            ->with([2, 3, 4])
            ->shouldReceive('setCrud')
            ->with(false)
            ->getMock();

        $mockForm->shouldReceive('getInputFilter')
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
            );

        $this->getMockFormHelper()
            ->shouldReceive('remove')
            ->with($mockForm, 'data->notice');

        $this->sut->shouldReceive('getTypeOfLicenceData')->andReturn(
            [
                'version'     => 1,
                'niFlag'      => 'N',
                'licenceType' => LicenceEntityService::LICENCE_TYPE_STANDARD_NATIONAL,
                'goodsOrPsv'  => LicenceEntityService::LICENCE_CATEGORY_PSV,
            ]
        );

        $id = 69;
        $data = [
            'id' => $id,
            'totAuthVehicles'       => 5,
            'totAuthSmallVehicles'  => 2,
            'totAuthMediumVehicles' => 3,
            'totAuthLargeVehicles'  => 0,
        ];

        $this->adapter
            ->shouldReceive('getVehicleCountByPsvType')
                ->with($id, VehicleEntityService::PSV_TYPE_SMALL)
                ->andReturn(2)
            ->shouldReceive('getVehicleCountByPsvType')
                ->with($id, VehicleEntityService::PSV_TYPE_MEDIUM)
                ->andReturn(2)
            ->shouldReceive('getVehicleCountByPsvType')
                ->with($id, VehicleEntityService::PSV_TYPE_LARGE)
                ->andReturn(1)
            ->shouldReceive('warnIfAuthorityExceeded');

        $this->assertSame($mockForm, $this->sut->alterForm($mockForm, $data));
    }

    protected function mockRowField($form, $name, $value)
    {
        $form->shouldReceive('get')
            ->with($name)
            ->andReturn(
                m::mock()
                    ->shouldReceive('get')
                    ->with('rows')
                    ->andReturn(
                        m::mock()
                            ->shouldReceive('getValue')
                            ->andReturn($value)
                            ->getMock()
                    )
                    ->getMock()
            );
    }
}
