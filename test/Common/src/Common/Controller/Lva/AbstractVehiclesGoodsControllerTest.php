<?php

namespace CommonTest\Controller\Lva;

use \Mockery as m;
use Common\Controller\Lva\Adapters\ApplicationVehicleGoodsAdapter;

/**
 * Test Abstract Vehicles Goods Controller
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class AbstractVehiclesGoodsControllerTest extends AbstractLvaControllerTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->mockController('\Common\Controller\Lva\AbstractVehiclesGoodsController');
    }

    /**
     * Get index
     *
     * @group abstractVehicleGoodsController
     */
    public function testGetIndexAction()
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

        $this->getMockFormHelper()
            ->shouldReceive('createForm')
            ->with('Lva\VehicleFilter')
            ->andReturn($filterForm);

        $this->sut
            ->shouldReceive('params')
            ->andReturn(
                m::mock()
                ->shouldReceive('fromQuery')
                ->with('vrm', 'All')
                ->andReturn('A')
                ->shouldReceive('fromQuery')
                ->with('specified', 'A')
                ->andReturn('A')
                ->shouldReceive('fromQuery')
                ->with('includeRemoved', 0)
                ->andReturn('A')
                ->shouldReceive('fromQuery')
                ->with('disc', 'A')
                ->andReturn('A')
                ->getMock()
            );

        $mockValidator = $this->mockService('oneRowInTablesRequired', 'setRows')
            ->with([0])
            ->shouldReceive('setCrud')
            ->with(false)
            ->getMock();

        $form = $this->createMockForm('Lva\GoodsVehicles');

        $form->shouldReceive('setData')
            ->with(
                []
            )
            ->andReturn($form)
            ->shouldReceive('get')
            ->with('table')
            ->andReturn(
                m::mock('\Zend\Form\Fieldset')
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
            );

        $this->getMockFormHelper()
            ->shouldReceive('populateFormTable');

        $this->mockService('Table', 'prepareTable')
            ->with('lva-vehicles', [])
            ->andReturn(m::mock('\Common\Service\Table\TableBuilder'));

        $this->sut->shouldReceive('getLicenceId')
            ->andReturn(123)
            ->shouldReceive('getIdentifier')
            ->andReturn(321)
            ->shouldReceive('getLvaEntityService')
            ->andReturn(
                m::mock()
                ->shouldReceive('getTotalVehicleAuthorisation')
                ->with(321)
                ->getMock()
            );

        $this->mockEntity('Licence', 'getVehiclesData')
            ->with(123)
            ->andReturn([]);

        $this->mockEntity('Licence', 'getVehiclesTotal')
            ->with(123)
            ->andReturn(0);

        $this->mockRender();

        $this->sut->indexAction();

        $this->assertEquals('vehicles', $this->view);
    }

    /**
     * Get index with filters
     *
     * @group abstractVehicleGoodsController
     * @dataProvider filtersForIndexDataProvider
     */
    public function testIndexActionWithFilters($filters, $licenceVehicle, $rows)
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

        $this->getMockFormHelper()
            ->shouldReceive('createForm')
            ->with('Lva\VehicleFilter')
            ->andReturn($filterForm);

        $this->sut
            ->shouldReceive('params')
            ->andReturn(
                m::mock()
                ->shouldReceive('fromQuery')
                ->with('vrm', 'All')
                ->andReturn($filters['vrm'])
                ->shouldReceive('fromQuery')
                ->with('specified', 'A')
                ->andReturn($filters['specified'])
                ->shouldReceive('fromQuery')
                ->with('includeRemoved', 0)
                ->andReturn($filters['includeRemoved'])
                ->shouldReceive('fromQuery')
                ->with('disc', 'A')
                ->andReturn($filters['disc'])
                ->getMock()
            );

        $mockValidator = $this->mockService('oneRowInTablesRequired', 'setRows')
            ->with([0])
            ->shouldReceive('setCrud')
            ->with(false)
            ->getMock();

        $form = $this->createMockForm('Lva\GoodsVehicles');

        $form->shouldReceive('setData')
            ->with(
                []
            )
            ->andReturn($form)
            ->shouldReceive('get')
            ->with('table')
            ->andReturn(
                m::mock('\Zend\Form\Fieldset')
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
            );

        $this->getMockFormHelper()
            ->shouldReceive('populateFormTable');

        $this->mockService('Table', 'prepareTable')
            ->with('lva-vehicles', $rows)
            ->andReturn(m::mock('\Common\Service\Table\TableBuilder'));

        $this->sut->shouldReceive('getLicenceId')
            ->andReturn(123)
            ->shouldReceive('getIdentifier')
            ->andReturn(321)
            ->shouldReceive('getLvaEntityService')
            ->andReturn(
                m::mock()
                ->shouldReceive('getTotalVehicleAuthorisation')
                ->with(321)
                ->getMock()
            );

        $this->mockEntity('Licence', 'getVehiclesData')
            ->with(123)
            ->andReturn([$licenceVehicle]);

        $this->mockEntity('Licence', 'getVehiclesTotal')
            ->with(123)
            ->andReturn(0);

        $this->mockRender();

        $this->sut->indexAction();

        $this->assertEquals('vehicles', $this->view);

    }

    /**
     * Data provider for testIndexActionWithFilters
     */
    public function filtersForIndexDataProvider()
    {
        $result = [[
            'id' => 1,
            'specifiedDate' => '2014-01-01',
            'removalDate' => '',
            'vrm' => 'VRM123',
            'discNo' => '1234'
        ]];
        return
            [
                [
                    ['vrm' => 'All', 'specified' => 'A', 'includeRemoved' => 0, 'disc' => 'All'],
                    [
                        'id' => 1,
                        'specifiedDate' => '2014-01-01',
                        'removalDate' => null,
                        'goodsDiscs' => [[
                            'discNo' => '1234'
                        ]],
                        'vehicle' => [
                            'vrm' => 'VRM123'
                        ]
                    ],
                    $result
                ],
                [
                    ['vrm' => 'X', 'specified' => 'A', 'includeRemoved' => 0, 'disc' => 'All'],
                    [
                        'id' => 1,
                        'specifiedDate' => '2014-01-01',
                        'removalDate' => null,
                        'goodsDiscs' => [[
                            'discNo' => '1234'
                        ]],
                        'vehicle' => [
                            'vrm' => 'VRM123'
                        ]
                    ],
                    []
                ],
                [
                    ['vrm' => 'All', 'specified' => 'Y', 'includeRemoved' => 0, 'disc' => 'All'],
                    [
                        'id' => 1,
                        'specifiedDate' => null,
                        'removalDate' => null,
                        'goodsDiscs' => [[
                            'discNo' => '1234'
                        ]],
                        'vehicle' => [
                            'vrm' => 'VRM123'
                        ]
                    ],
                    []
                ],
                [
                    ['vrm' => 'All', 'specified' => 'N', 'includeRemoved' => 0, 'disc' => 'All'],
                    [
                        'id' => 1,
                        'specifiedDate' => '2014-01-01',
                        'removalDate' => null,
                        'goodsDiscs' => [[
                            'discNo' => '1234'
                        ]],
                        'vehicle' => [
                            'vrm' => 'VRM123'
                        ]
                    ],
                    null
                ],
                [
                    ['vrm' => 'All', 'specified' => 'A', 'includeRemoved' => '', 'disc' => 'All'],
                    [
                        'id' => 1,
                        'specifiedDate' => '2014-01-01',
                        'removalDate' => '2014-01-01',
                        'goodsDiscs' => [[
                            'discNo' => '1234'
                        ]],
                        'vehicle' => [
                            'vrm' => 'VRM123'
                        ]
                    ],
                    []
                ],
                [
                    ['vrm' => 'All', 'specified' => 'A', 'includeRemoved' => 0, 'disc' => 'Y'],
                    [
                        'id' => 1,
                        'specifiedDate' => '2014-01-01',
                        'removalDate' => null,
                        'goodsDiscs' => [],
                        'vehicle' => [
                            'vrm' => 'VRM123'
                        ]
                    ],
                    []
                ],
                [
                    ['vrm' => 'All', 'specified' => 'A', 'includeRemoved' => 0, 'disc' => 'N'],
                    [
                        'id' => 1,
                        'specifiedDate' => '2014-01-01',
                        'removalDate' => null,
                        'goodsDiscs' => [[
                            'discNo' => '1234'
                        ]],
                        'vehicle' => [
                            'vrm' => 'VRM123'
                        ]
                    ],
                    []
                ]
            ];
    }


    /**
     * Test index action with post
     *
     * @group abstractVehicleGoodsController1
     */
    public function testIndexActionWithPost()
    {
        $formData = ['data' => ['hasEnteredReg' => 'Y', 'version' => 1], 'table' => ['rows' => 1]];
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

        $this->getMockFormHelper()
            ->shouldReceive('createForm')
            ->with('Lva\VehicleFilter')
            ->andReturn($filterForm);

        $this->request
            ->shouldReceive('isPost')
            ->andReturn(true)
            ->shouldReceive('getPost')
            ->andReturn(
                $formData
            )
            ->getMock();

        $mockValidator = $this->mockService('oneRowInTablesRequired', 'setRows')
            ->with([1])
            ->shouldReceive('setCrud')
            ->with(false)
            ->getMock();

        $form = $this->createMockForm('Lva\GoodsVehicles');

        $form->shouldReceive('setData')
            ->with(
                []
            )
            ->andReturn($form)
            ->shouldReceive('get')
            ->with('table')
            ->andReturn(
                m::mock('\Zend\Form\Fieldset')
                ->shouldReceive('get')
                ->with('rows')
                ->andReturn(
                    m::mock()
                    ->shouldReceive('getValue')
                    ->andReturn(1)
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
            ->shouldReceive('isValid')
            ->andReturn(true)
            ->shouldReceive('getData')
            ->andReturn($formData);

        $this->sut
            ->shouldReceive('getAdapter')
            ->andReturn(
                m::mock()
                ->shouldReceive('populateForm')
                ->with($this->request, ['hasEnteredReg' => 'Y', 'version' => 1], $form)
                ->andReturn($form)
                ->getMock()
            )
            ->shouldReceive('getIdentifier')
            ->andReturn(1)
            ->shouldReceive('getLvaEntityService')
            ->andReturn(
                m::mock()
                ->shouldReceive('getHeaderData')
                ->with(1)
                ->andReturn(['hasEnteredReg' => 'Y', 'version' => 1])
                ->shouldReceive('save')
                ->with(['hasEnteredReg' => 'Y', 'version' => 1, 'id' => 1])
                ->getMock()
            )
            ->shouldReceive('params')
            ->andReturn(
                m::mock()
                ->shouldReceive('fromQuery')
                ->with('vrm', 'All')
                ->andReturn('A')
                ->shouldReceive('fromQuery')
                ->with('specified', 'A')
                ->andReturn('A')
                ->shouldReceive('fromQuery')
                ->with('includeRemoved', 0)
                ->andReturn('A')
                ->shouldReceive('fromQuery')
                ->with('disc', 'A')
                ->andReturn('A')
                ->getMock()
            )
            ->shouldReceive('completeSection')
            ->with('vehicles');

        $this->getMockFormHelper()
            ->shouldReceive('populateFormTable');

        $this->mockService('Table', 'prepareTable')
            ->with('lva-vehicles', [])
            ->andReturn(m::mock('\Common\Service\Table\TableBuilder'));

        $this->sut->shouldReceive('getLicenceId')
            ->andReturn(123)
            ->shouldReceive('getIdentifier')
            ->andReturn(321)
            ->shouldReceive('getLvaEntityService')
            ->andReturn(
                m::mock()
                ->shouldReceive('getTotalVehicleAuthorisation')
                ->with(321)
                ->getMock()
            );

        $this->mockEntity('Licence', 'getVehiclesData')
            ->with(123)
            ->andReturn([]);

        $this->mockEntity('Licence', 'getVehiclesTotal')
            ->with(123)
            ->andReturn(0);

        $this->sut->indexAction();
    }

    /**
     * Test set / get vehicle goods adapter
     *
     * @group abstractVehicleGoodsController1
     */
    public function testSetAdapter()
    {
        $adapter = new ApplicationVehicleGoodsAdapter();
        $this->sut->setAdapter($adapter);
        $this->assertSame($this->sut->getAdapter(), $adapter);
    }

    public function testBasicAddAction()
    {
        $form = $this->createMockForm('Lva\GoodsVehiclesVehicle');

        $specifiedDate = m::mock();
        $removalDate = m::mock();

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

        $this->mockRender();

        $this->sut->shouldReceive('params')
            ->with('child_id')
            ->andReturn(50);

        $this->sut->addAction();

        $this->assertEquals('add_vehicles', $this->view);
    }

    public function testBasicEditActionWithPostToRemovedVehicle()
    {
        // Params
        $id = 1;

        // Stubbed data
        $stubbedVehicleFormData = [
            'removalDate' => '2014-01-01'
        ];

        // Mocks
        $mockLicenceVehicle = m::mock();
        $this->sm->setService('Entity\LicenceVehicle', $mockLicenceVehicle);
        $mockFlashMessenger = m::mock();
        $this->sm->setService('Helper\FlashMessenger', $mockFlashMessenger);

        // Expectations
        $mockLicenceVehicle->shouldReceive('getVehicle')
            ->with($id)
            ->andReturn($stubbedVehicleFormData);

        $mockFlashMessenger->shouldReceive('addErrorMessage')
            ->with('cant-edit-removed-vehicle');

        $this->request->shouldReceive('isPost')
            ->andReturn(true);

        $this->sut
            ->shouldReceive('params')
            ->with('child_id')
            ->andReturn($id);

        $this->sut->shouldReceive('redirect->toRoute')
            ->with(null, [], [], true)
            ->andReturn('REDIRECT');

        $this->assertEquals('REDIRECT', $this->sut->editAction());
    }

    public function testBasicEditAction()
    {
        // Params
        $id = 1;

        // Stubbed data
        $stubbedVehicleFormData = [
            'removalDate' => '2014-01-01',
            'vehicle' => 'ABC',
            'goodsDiscs' => 'Foo'
        ];

        $expectedDiscParam = [
            'removalDate' => '2014-01-01',
            'goodsDiscs' => 'Foo'
        ];

        // Mocks
        $form = $this->createMockForm('Lva\GoodsVehiclesVehicle');
        $mockLicenceVehicle = m::mock();
        $this->sm->setService('Entity\LicenceVehicle', $mockLicenceVehicle);
        $mockDateHelper = m::mock();
        $this->sm->setService('Helper\Date', $mockDateHelper);
        $dateObject = m::mock();

        // Expectations
        $mockDateHelper->shouldReceive('getDateObject')
            ->andReturn($dateObject);

        $mockLicenceVehicle->shouldReceive('getVehicle')
            ->with($id)
            ->andReturn($stubbedVehicleFormData);

        $this->request->shouldReceive('isPost')
            ->andReturn(false);

        $this->sut
            ->shouldReceive('params')
            ->with('child_id')
            ->andReturn($id);

        $form->shouldReceive('setData')
            ->andReturn($form)
            ->shouldReceive('get')
            ->with('form-actions')
            ->andReturn(
                m::mock()
                ->shouldReceive('remove')
                ->with('submit')
                ->shouldReceive('get')
                ->with('cancel')
                ->andReturn(
                    m::mock()
                    ->shouldReceive('setAttribute')
                    ->with('disabled', false)
                    ->getMock()
                )
                ->getMock()
            );

        $this->getMockFormHelper()
            ->shouldReceive('disableElements')
            ->with($form);

        $this->sut->shouldReceive('isDiscPending')
            ->with($expectedDiscParam)
            ->andReturn(true)
            ->shouldReceive('alterVehicleForm')
            ->with($form, 'edit')
            ->andReturn($form)
            ->shouldReceive('setDefaultDates')
            ->with($form, $dateObject)
            ->andReturn($form);

        $this->mockRender();

        $this->sut->editAction();

        $this->assertEquals('edit_vehicles', $this->view);
    }
}
