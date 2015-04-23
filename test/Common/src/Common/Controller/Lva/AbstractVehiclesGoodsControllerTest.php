<?php

/**
 * Test Abstract Vehicles Goods Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Controller\Lva;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use CommonTest\Bootstrap;

/**
 * Test Abstract Vehicles Goods Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class AbstractVehiclesGoodsControllerTest extends MockeryTestCase
{
    protected $sut;

    protected $sm;

    protected $adapter;

    public function setUp()
    {
        $this->sut = m::mock('\Common\Controller\Lva\AbstractVehiclesGoodsController')
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $this->sm = Bootstrap::getServiceManager();
        $this->adapter = m::mock('\Common\Controller\Lva\Interfaces\AdapterInterface');

        $this->sut->setServiceLocator($this->sm);
        $this->sut->setAdapter($this->adapter);
    }

    public function testIndexAction()
    {
        $id = 111;
        $licenceId = 111;
        $stubbedFormData = [
            'foo' => 'bar',
            'query' => [
                'page' => 1
            ]
        ];
        $stubbedQuery = [
            'page' => 1
        ];
        $licenceVehicle = [
            'bar' => 'foo',
            'goodsDiscs' => 'cake',
            'vehicle' => [
                'id' => 222,
                'foo' => 'bar'
            ]
        ];
        $stubbedTableData = [
            'Results' => [
                $licenceVehicle
            ]
        ];

        $preparedTableData = [
            'Results' => [
                [
                    'bar' => 'foo',
                    'foo' => 'bar',
                    'discNo' => 'Pending'
                ]
            ]
        ];

        // Mocks
        $mockTable = m::mock();
        $mockForm = m::mock();
        $mockFilterForm = m::mock();
        $mockRequest = m::mock();
        $mockTableBuilder = m::mock();
        $formServiceManager = m::mock('\Common\FormService\FormServiceManager')->makePartial();
        $mockFormService = m::mock('\Common\FormService\FormServiceInterface');
        $mockFilterFormService = m::mock('\Common\FormService\FormServiceInterface');
        $mockLicence = m::mock();
        $mockGuidance = m::mock();
        $mockScript = m::mock();

        $this->sm->setService('Script', $mockScript);
        $this->sm->setService('Helper\Guidance', $mockGuidance);
        $this->sm->setService('Entity\Licence', $mockLicence);
        $this->sm->setService('Table', $mockTableBuilder);
        $this->sm->setService('FormServiceManager', $formServiceManager);
        $formServiceManager->setService('lva--goods-vehicles', $mockFormService);
        $formServiceManager->setService('lva--goods-vehicles-filters', $mockFilterFormService);

        // Expectations
        $this->sut->shouldReceive('getRequest')
            ->andReturn($mockRequest)
            ->shouldReceive('getIdentifier')
            ->andReturn($id)
            ->shouldReceive('isDiscPending')
            ->with($licenceVehicle)
            ->andReturn(true)
            ->shouldReceive('getLicenceId')
            ->andReturn($licenceId)
            ->shouldReceive('getLvaEntityService')
            ->andReturn($mockLicence)
            ->shouldReceive('render')
            ->once()
            ->with('vehicles', $mockForm, ['filterForm' => $mockFilterForm])
            ->andReturn('RESPONSE');

        $mockRequest->shouldReceive('isPost')
            ->andReturn(false)
            ->shouldReceive('getQuery')
            ->andReturn($stubbedQuery);

        $this->adapter->shouldReceive('getFormData')
            ->once()
            ->with(111)
            ->andReturn($stubbedFormData)
            ->shouldReceive('getFilteredVehiclesData')
            ->once()
            ->with(111, $stubbedQuery)
            ->andReturn($stubbedTableData)
            ->shouldReceive('alterVehcileTable')
            ->with($mockTable, $id)
            ->andReturn($mockTable);

        $mockFormService->shouldReceive('getForm')
            ->once()
            ->with($mockTable, false)
            ->andReturn($mockForm);

        $mockForm->shouldReceive('setData')
            ->once()
            ->with($stubbedFormData)
            ->andReturnSelf();

        $mockTableBuilder->shouldReceive('prepareTable')
            ->once()
            ->with('lva-vehicles', $preparedTableData, ['page' => 1, 'query' => ['page' => 1]])
            ->andReturn($mockTable);

        $mockLicence->shouldReceive('getVehiclesTotal')
            ->once()
            ->with(111)
            ->andReturn(10)
            ->shouldReceive('getTotalVehicleAuthorisation')
            ->once()
            ->with(111)
            ->andReturn(9);

        $mockGuidance->shouldReceive('append')
            ->once()
            ->with('more-vehicles-than-authorisation');

        $mockFilterFormService->shouldReceive('getForm')
            ->andReturn($mockFilterForm);

        $mockFilterForm->shouldReceive('setData')
            ->once()
            ->with(['page' => 1, 'limit' => 10]);

        $mockScript->shouldReceive('loadFiles')
            ->once()
            ->with(['lva-crud', 'vehicle-goods', 'forms/filter']);

        // Assertions
        $response = $this->sut->indexAction();

        $this->assertEquals('RESPONSE', $response);
    }

    public function testIndexActionPostInvalid()
    {
        $id = 111;
        $licenceId = 111;
        $stubbedFormData = [
            'table' => 'foo',
            'foo' => 'bar',
            'query' => [
                'page' => 1
            ]
        ];
        $stubbedQuery = [
            'page' => 1
        ];
        $licenceVehicle = [
            'bar' => 'foo',
            'goodsDiscs' => 'cake',
            'vehicle' => [
                'foo' => 'bar'
            ]
        ];
        $licenceVehicle2 = [
            'bar' => 'foo',
            'goodsDiscs' => [
                [
                    'discNo' => 'foo'
                ]
            ],
            'vehicle' => [
                'foo' => 'bar'
            ]
        ];
        $licenceVehicle3 = [
            'bar' => 'foo',
            'vehicle' => [
                'foo' => 'bar'
            ]
        ];
        $stubbedTableData = [
            'Results' => [
                $licenceVehicle,
                $licenceVehicle2,
                $licenceVehicle3
            ]
        ];

        $preparedTableData = [
            'Results' => [
                [
                    'bar' => 'foo',
                    'foo' => 'bar',
                    'discNo' => 'Pending'
                ],
                [
                    'bar' => 'foo',
                    'foo' => 'bar',
                    'discNo' => 'foo'
                ],
                [
                    'bar' => 'foo',
                    'foo' => 'bar',
                    'discNo' => ''
                ]
            ]
        ];

        // Mocks
        $mockTable = m::mock();
        $mockForm = m::mock();
        $mockFilterForm = m::mock();
        $mockRequest = m::mock();
        $mockTableBuilder = m::mock();
        $formServiceManager = m::mock('\Common\FormService\FormServiceManager')->makePartial();
        $mockFormService = m::mock('\Common\FormService\FormServiceInterface');
        $mockFilterFormService = m::mock('\Common\FormService\FormServiceInterface');
        $mockLicence = m::mock();
        $mockGuidance = m::mock();
        $mockScript = m::mock();

        $this->sm->setService('Script', $mockScript);
        $this->sm->setService('Helper\Guidance', $mockGuidance);
        $this->sm->setService('Entity\Licence', $mockLicence);
        $this->sm->setService('Table', $mockTableBuilder);
        $this->sm->setService('FormServiceManager', $formServiceManager);
        $formServiceManager->setService('lva--goods-vehicles', $mockFormService);
        $formServiceManager->setService('lva--goods-vehicles-filters', $mockFilterFormService);

        // Expectations
        $this->sut->shouldReceive('getRequest')
            ->andReturn($mockRequest)
            ->shouldReceive('getCrudAction')
            ->andReturn(null)
            ->shouldReceive('getIdentifier')
            ->andReturn($id)
            ->shouldReceive('isDiscPending')
            ->with($licenceVehicle)
            ->andReturn(true)
            ->shouldReceive('isDiscPending')
            ->with($licenceVehicle2)
            ->andReturn(false)
            ->shouldReceive('isDiscPending')
            ->with($licenceVehicle3)
            ->andReturn(false)
            ->shouldReceive('getLicenceId')
            ->andReturn($licenceId)
            ->shouldReceive('getLvaEntityService')
            ->andReturn($mockLicence)
            ->shouldReceive('render')
            ->once()
            ->with('vehicles', $mockForm, ['filterForm' => $mockFilterForm])
            ->andReturn('RESPONSE');

        $mockRequest->shouldReceive('isPost')
            ->andReturn(true)
            ->shouldReceive('getPost')
            ->with('query')
            ->andReturn($stubbedQuery)
            ->shouldReceive('getPost')
            ->andReturn($stubbedFormData)
            ->shouldReceive('getQuery')
            ->andReturn($stubbedQuery);

        $this->adapter
            ->shouldReceive('getFilteredVehiclesData')
            ->once()
            ->with(111, $stubbedQuery)
            ->andReturn($stubbedTableData)
            ->shouldReceive('alterVehcileTable')
            ->with($mockTable, $id)
            ->andReturn($mockTable);

        $mockFormService->shouldReceive('getForm')
            ->once()
            ->with($mockTable, false)
            ->andReturn($mockForm);

        $mockForm->shouldReceive('setData')
            ->once()
            ->with($stubbedFormData)
            ->andReturnSelf()
            ->shouldReceive('isValid')
            ->andReturn(false);

        $mockTableBuilder->shouldReceive('prepareTable')
            ->once()
            ->with('lva-vehicles', $preparedTableData, ['page' => 1, 'query' => ['page' => 1]])
            ->andReturn($mockTable);

        $mockLicence->shouldReceive('getVehiclesTotal')
            ->once()
            ->with(111)
            ->andReturn(10)
            ->shouldReceive('getTotalVehicleAuthorisation')
            ->once()
            ->with(111)
            ->andReturn(9);

        $mockGuidance->shouldReceive('append')
            ->once()
            ->with('more-vehicles-than-authorisation');

        $mockFilterFormService->shouldReceive('getForm')
            ->andReturn($mockFilterForm);

        $mockFilterForm->shouldReceive('setData')
            ->once()
            ->with(['page' => 1, 'limit' => 10]);

        $mockScript->shouldReceive('loadFiles')
            ->once()
            ->with(['lva-crud', 'vehicle-goods', 'forms/filter']);

        // Assertions
        $response = $this->sut->indexAction();

        $this->assertEquals('RESPONSE', $response);
    }

    public function testIndexActionPostFail()
    {
        $id = 111;
        $licenceId = 111;
        $stubbedFormData = [
            'table' => 'foo',
            'foo' => 'bar',
            'query' => [
                'page' => 1
            ]
        ];
        $stubbedQuery = [
            'page' => 1
        ];
        $licenceVehicle = [
            'bar' => 'foo',
            'goodsDiscs' => 'cake',
            'vehicle' => [
                'foo' => 'bar'
            ]
        ];
        $licenceVehicle2 = [
            'bar' => 'foo',
            'goodsDiscs' => [
                [
                    'discNo' => 'foo'
                ]
            ],
            'vehicle' => [
                'foo' => 'bar'
            ]
        ];
        $licenceVehicle3 = [
            'bar' => 'foo',
            'vehicle' => [
                'foo' => 'bar'
            ]
        ];
        $stubbedTableData = [
            'Results' => [
                $licenceVehicle,
                $licenceVehicle2,
                $licenceVehicle3
            ]
        ];

        $preparedTableData = [
            'Results' => [
                [
                    'bar' => 'foo',
                    'foo' => 'bar',
                    'discNo' => 'Pending'
                ],
                [
                    'bar' => 'foo',
                    'foo' => 'bar',
                    'discNo' => 'foo'
                ],
                [
                    'bar' => 'foo',
                    'foo' => 'bar',
                    'discNo' => ''
                ]
            ]
        ];

        // Mocks
        $mockTable = m::mock();
        $mockForm = m::mock();
        $mockFilterForm = m::mock();
        $mockRequest = m::mock();
        $mockTableBuilder = m::mock();
        $mockFormService = m::mock('\Common\FormService\FormServiceInterface');
        $mockFilterFormService = m::mock('\Common\FormService\FormServiceInterface');
        $mockLicence = m::mock();
        $mockGuidance = m::mock();
        $mockScript = m::mock();
        $mockResponse = m::mock();
        $mockBusinessService = m::mock('\Common\BusinessService\BusinessServiceInterface');
        $mockFashMessenger = m::mock();

        $formServiceManager = m::mock('\Common\FormService\FormServiceManager')->makePartial();
        $businessServiceManager = m::mock('\Common\BusinessService\BusinessServiceManager')->makePartial();

        $this->sm->setService('Helper\FlashMessenger', $mockFashMessenger);
        $this->sm->setService('Script', $mockScript);
        $this->sm->setService('Helper\Guidance', $mockGuidance);
        $this->sm->setService('Entity\Licence', $mockLicence);
        $this->sm->setService('Table', $mockTableBuilder);
        $this->sm->setService('FormServiceManager', $formServiceManager);
        $this->sm->setService('BusinessServiceManager', $businessServiceManager);
        $formServiceManager->setService('lva--goods-vehicles', $mockFormService);
        $formServiceManager->setService('lva--goods-vehicles-filters', $mockFilterFormService);
        $businessServiceManager->setService('Lva\GoodsVehicles', $mockBusinessService);

        // Expectations
        $this->sut->shouldReceive('getRequest')
            ->andReturn($mockRequest)
            ->shouldReceive('getCrudAction')
            ->andReturn(null)
            ->shouldReceive('getIdentifier')
            ->andReturn($id)
            ->shouldReceive('isDiscPending')
            ->with($licenceVehicle)
            ->andReturn(true)
            ->shouldReceive('isDiscPending')
            ->with($licenceVehicle2)
            ->andReturn(false)
            ->shouldReceive('isDiscPending')
            ->with($licenceVehicle3)
            ->andReturn(false)
            ->shouldReceive('getLicenceId')
            ->andReturn($licenceId)
            ->shouldReceive('getLvaEntityService')
            ->andReturn($mockLicence)
            ->shouldReceive('render')
            ->once()
            ->with('vehicles', $mockForm, ['filterForm' => $mockFilterForm])
            ->andReturn('RESPONSE');

        $mockRequest->shouldReceive('isPost')
            ->andReturn(true)
            ->shouldReceive('getPost')
            ->with('query')
            ->andReturn($stubbedQuery)
            ->shouldReceive('getPost')
            ->andReturn($stubbedFormData)
            ->shouldReceive('getQuery')
            ->andReturn($stubbedQuery);

        $this->adapter
            ->shouldReceive('getFilteredVehiclesData')
            ->once()
            ->with(111, $stubbedQuery)
            ->andReturn($stubbedTableData)
            ->shouldReceive('alterVehcileTable')
            ->with($mockTable, $id)
            ->andReturn($mockTable);

        $mockFormService->shouldReceive('getForm')
            ->once()
            ->with($mockTable, false)
            ->andReturn($mockForm);

        $mockForm->shouldReceive('setData')
            ->once()
            ->with($stubbedFormData)
            ->andReturnSelf()
            ->shouldReceive('isValid')
            ->andReturn(true)
            ->shouldReceive('getData')
            ->andReturn(['form' => 'data']);

        $mockTableBuilder->shouldReceive('prepareTable')
            ->once()
            ->with('lva-vehicles', $preparedTableData, ['page' => 1, 'query' => ['page' => 1]])
            ->andReturn($mockTable);

        $mockLicence->shouldReceive('getVehiclesTotal')
            ->once()
            ->with(111)
            ->andReturn(10)
            ->shouldReceive('getTotalVehicleAuthorisation')
            ->once()
            ->with(111)
            ->andReturn(9);

        $mockGuidance->shouldReceive('append')
            ->once()
            ->with('more-vehicles-than-authorisation');

        $mockFilterFormService->shouldReceive('getForm')
            ->andReturn($mockFilterForm);

        $mockFilterForm->shouldReceive('setData')
            ->once()
            ->with(['page' => 1, 'limit' => 10]);

        $mockScript->shouldReceive('loadFiles')
            ->once()
            ->with(['lva-crud', 'vehicle-goods', 'forms/filter']);

        $mockBusinessService->shouldReceive('process')
            ->with(['id' => 111, 'data' => ['form' => 'data']])
            ->andReturn($mockResponse);

        $mockResponse->shouldReceive('isOk')
            ->andReturn(false)
            ->shouldReceive('getMessage')
            ->andReturn('msg');

        $mockFashMessenger->shouldReceive('addErrorMessage')
            ->with('msg');

        // Assertions
        $response = $this->sut->indexAction();

        $this->assertEquals('RESPONSE', $response);
    }

    public function testIndexActionPostSuccess()
    {
        $id = 111;
        $licenceId = 111;
        $stubbedFormData = [
            'table' => 'foo',
            'foo' => 'bar',
            'query' => [
                'page' => 1
            ]
        ];
        $stubbedQuery = [
            'page' => 1
        ];
        $licenceVehicle = [
            'bar' => 'foo',
            'goodsDiscs' => 'cake',
            'vehicle' => [
                'foo' => 'bar'
            ]
        ];
        $licenceVehicle2 = [
            'bar' => 'foo',
            'goodsDiscs' => [
                [
                    'discNo' => 'foo'
                ]
            ],
            'vehicle' => [
                'foo' => 'bar'
            ]
        ];
        $licenceVehicle3 = [
            'bar' => 'foo',
            'vehicle' => [
                'foo' => 'bar'
            ]
        ];
        $stubbedTableData = [
            'Results' => [
                $licenceVehicle,
                $licenceVehicle2,
                $licenceVehicle3
            ]
        ];

        $preparedTableData = [
            'Results' => [
                [
                    'bar' => 'foo',
                    'foo' => 'bar',
                    'discNo' => 'Pending'
                ],
                [
                    'bar' => 'foo',
                    'foo' => 'bar',
                    'discNo' => 'foo'
                ],
                [
                    'bar' => 'foo',
                    'foo' => 'bar',
                    'discNo' => ''
                ]
            ]
        ];

        // Mocks
        $mockTable = m::mock();
        $mockForm = m::mock();
        $mockRequest = m::mock();
        $mockTableBuilder = m::mock();
        $mockFormService = m::mock('\Common\FormService\FormServiceInterface');
        $mockResponse = m::mock();
        $mockBusinessService = m::mock('\Common\BusinessService\BusinessServiceInterface');

        $formServiceManager = m::mock('\Common\FormService\FormServiceManager')->makePartial();
        $businessServiceManager = m::mock('\Common\BusinessService\BusinessServiceManager')->makePartial();

        $this->sm->setService('Table', $mockTableBuilder);
        $this->sm->setService('FormServiceManager', $formServiceManager);
        $this->sm->setService('BusinessServiceManager', $businessServiceManager);
        $formServiceManager->setService('lva--goods-vehicles', $mockFormService);
        $businessServiceManager->setService('Lva\GoodsVehicles', $mockBusinessService);

        // Expectations
        $this->sut->shouldReceive('getRequest')
            ->andReturn($mockRequest)
            ->shouldReceive('getCrudAction')
            ->andReturn(null)
            ->shouldReceive('getIdentifier')
            ->andReturn($id)
            ->shouldReceive('isDiscPending')
            ->with($licenceVehicle)
            ->andReturn(true)
            ->shouldReceive('isDiscPending')
            ->with($licenceVehicle2)
            ->andReturn(false)
            ->shouldReceive('isDiscPending')
            ->with($licenceVehicle3)
            ->andReturn(false)
            ->shouldReceive('getLicenceId')
            ->andReturn($licenceId)
            ->shouldReceive('completeSection')
            ->with('vehicles')
            ->andReturn('RESPONSE')
            ->shouldReceive('postSave')
            ->with('vehicles');

        $mockRequest->shouldReceive('isPost')
            ->andReturn(true)
            ->shouldReceive('getPost')
            ->with('query')
            ->andReturn($stubbedQuery)
            ->shouldReceive('getPost')
            ->andReturn($stubbedFormData)
            ->shouldReceive('getQuery')
            ->andReturn($stubbedQuery);

        $this->adapter
            ->shouldReceive('getFilteredVehiclesData')
            ->once()
            ->with(111, $stubbedQuery)
            ->andReturn($stubbedTableData)
            ->shouldReceive('alterVehcileTable')
            ->with($mockTable, $id)
            ->andReturn($mockTable);

        $mockFormService->shouldReceive('getForm')
            ->once()
            ->with($mockTable, false)
            ->andReturn($mockForm);

        $mockForm->shouldReceive('setData')
            ->once()
            ->with($stubbedFormData)
            ->andReturnSelf()
            ->shouldReceive('isValid')
            ->andReturn(true)
            ->shouldReceive('getData')
            ->andReturn(['form' => 'data']);

        $mockTableBuilder->shouldReceive('prepareTable')
            ->once()
            ->with('lva-vehicles', $preparedTableData, ['page' => 1, 'query' => ['page' => 1]])
            ->andReturn($mockTable);

        $mockBusinessService->shouldReceive('process')
            ->with(['id' => 111, 'data' => ['form' => 'data']])
            ->andReturn($mockResponse);

        $mockResponse->shouldReceive('isOk')
            ->andReturn(true);

        // Assertions
        $response = $this->sut->indexAction();

        $this->assertEquals('RESPONSE', $response);
    }

    public function testIndexActionPostSuccessCrud()
    {
        $id = 111;
        $licenceId = 111;
        $stubbedFormData = [
            'table' => 'foo',
            'foo' => 'bar',
            'query' => [
                'page' => 1
            ]
        ];
        $stubbedQuery = [
            'page' => 1
        ];
        $licenceVehicle = [
            'bar' => 'foo',
            'goodsDiscs' => 'cake',
            'vehicle' => [
                'foo' => 'bar'
            ]
        ];
        $licenceVehicle2 = [
            'bar' => 'foo',
            'goodsDiscs' => [
                [
                    'discNo' => 'foo'
                ]
            ],
            'vehicle' => [
                'foo' => 'bar'
            ]
        ];
        $licenceVehicle3 = [
            'bar' => 'foo',
            'vehicle' => [
                'foo' => 'bar'
            ]
        ];
        $stubbedTableData = [
            'Results' => [
                $licenceVehicle,
                $licenceVehicle2,
                $licenceVehicle3
            ]
        ];

        $preparedTableData = [
            'Results' => [
                [
                    'bar' => 'foo',
                    'foo' => 'bar',
                    'discNo' => 'Pending'
                ],
                [
                    'bar' => 'foo',
                    'foo' => 'bar',
                    'discNo' => 'foo'
                ],
                [
                    'bar' => 'foo',
                    'foo' => 'bar',
                    'discNo' => ''
                ]
            ]
        ];

        // Mocks
        $mockTable = m::mock();
        $mockForm = m::mock();
        $mockRequest = m::mock();
        $mockTableBuilder = m::mock();
        $mockFormService = m::mock('\Common\FormService\FormServiceInterface');
        $mockResponse = m::mock();
        $mockBusinessService = m::mock('\Common\BusinessService\BusinessServiceInterface');

        $formServiceManager = m::mock('\Common\FormService\FormServiceManager')->makePartial();
        $businessServiceManager = m::mock('\Common\BusinessService\BusinessServiceManager')->makePartial();

        $this->sm->setService('Table', $mockTableBuilder);
        $this->sm->setService('FormServiceManager', $formServiceManager);
        $this->sm->setService('BusinessServiceManager', $businessServiceManager);
        $formServiceManager->setService('lva--goods-vehicles', $mockFormService);
        $businessServiceManager->setService('Lva\GoodsVehicles', $mockBusinessService);

        // Expectations
        $this->sut->shouldReceive('getRequest')
            ->andReturn($mockRequest)
            ->shouldReceive('getCrudAction')
            ->andReturn('add')
            ->shouldReceive('getIdentifier')
            ->andReturn($id)
            ->shouldReceive('isDiscPending')
            ->with($licenceVehicle)
            ->andReturn(true)
            ->shouldReceive('isDiscPending')
            ->with($licenceVehicle2)
            ->andReturn(false)
            ->shouldReceive('isDiscPending')
            ->with($licenceVehicle3)
            ->andReturn(false)
            ->shouldReceive('getLicenceId')
            ->andReturn($licenceId)
            ->shouldReceive('postSave')
            ->with('vehicles')
            ->shouldReceive('getActionFromCrudAction')
            ->with('add')
            ->andReturn('add')
            ->shouldReceive('checkForAlternativeCrudAction')
            ->with('add')
            ->andReturn(null)
            ->shouldReceive('handleCrudAction')
            ->with('add', ['add', 'print-vehicles'])
            ->andReturn('RESPONSE');

        $mockRequest->shouldReceive('isPost')
            ->andReturn(true)
            ->shouldReceive('getPost')
            ->with('query')
            ->andReturn($stubbedQuery)
            ->shouldReceive('getPost')
            ->andReturn($stubbedFormData)
            ->shouldReceive('getQuery')
            ->andReturn($stubbedQuery);

        $this->adapter
            ->shouldReceive('getFilteredVehiclesData')
            ->once()
            ->with(111, $stubbedQuery)
            ->andReturn($stubbedTableData)
            ->shouldReceive('alterVehcileTable')
            ->with($mockTable, $id)
            ->andReturn($mockTable);

        $mockFormService->shouldReceive('getForm')
            ->once()
            ->with($mockTable, true)
            ->andReturn($mockForm);

        $mockForm->shouldReceive('setData')
            ->once()
            ->with($stubbedFormData)
            ->andReturnSelf()
            ->shouldReceive('isValid')
            ->andReturn(true)
            ->shouldReceive('getData')
            ->andReturn(['form' => 'data']);

        $mockTableBuilder->shouldReceive('prepareTable')
            ->once()
            ->with('lva-vehicles', $preparedTableData, ['page' => 1, 'query' => ['page' => 1]])
            ->andReturn($mockTable);

        $mockBusinessService->shouldReceive('process')
            ->with(['id' => 111, 'data' => ['form' => 'data']])
            ->andReturn($mockResponse);

        $mockResponse->shouldReceive('isOk')
            ->andReturn(true);

        // Assertions
        $response = $this->sut->indexAction();

        $this->assertEquals('RESPONSE', $response);
    }

    public function testIndexActionPostSuccessAlternativeCrud()
    {
        $id = 111;
        $licenceId = 111;
        $stubbedFormData = [
            'table' => 'foo',
            'foo' => 'bar',
            'query' => [
                'page' => 1
            ]
        ];
        $stubbedQuery = [
            'page' => 1
        ];
        $licenceVehicle = [
            'bar' => 'foo',
            'goodsDiscs' => 'cake',
            'vehicle' => [
                'foo' => 'bar'
            ]
        ];
        $licenceVehicle2 = [
            'bar' => 'foo',
            'goodsDiscs' => [
                [
                    'discNo' => 'foo'
                ]
            ],
            'vehicle' => [
                'foo' => 'bar'
            ]
        ];
        $licenceVehicle3 = [
            'bar' => 'foo',
            'vehicle' => [
                'foo' => 'bar'
            ]
        ];
        $stubbedTableData = [
            'Results' => [
                $licenceVehicle,
                $licenceVehicle2,
                $licenceVehicle3
            ]
        ];

        $preparedTableData = [
            'Results' => [
                [
                    'bar' => 'foo',
                    'foo' => 'bar',
                    'discNo' => 'Pending'
                ],
                [
                    'bar' => 'foo',
                    'foo' => 'bar',
                    'discNo' => 'foo'
                ],
                [
                    'bar' => 'foo',
                    'foo' => 'bar',
                    'discNo' => ''
                ]
            ]
        ];

        // Mocks
        $mockTable = m::mock();
        $mockForm = m::mock();
        $mockRequest = m::mock();
        $mockTableBuilder = m::mock();
        $mockFormService = m::mock('\Common\FormService\FormServiceInterface');
        $mockResponse = m::mock();
        $mockBusinessService = m::mock('\Common\BusinessService\BusinessServiceInterface');

        $formServiceManager = m::mock('\Common\FormService\FormServiceManager')->makePartial();
        $businessServiceManager = m::mock('\Common\BusinessService\BusinessServiceManager')->makePartial();

        $this->sm->setService('Table', $mockTableBuilder);
        $this->sm->setService('FormServiceManager', $formServiceManager);
        $this->sm->setService('BusinessServiceManager', $businessServiceManager);
        $formServiceManager->setService('lva--goods-vehicles', $mockFormService);
        $businessServiceManager->setService('Lva\GoodsVehicles', $mockBusinessService);

        // Expectations
        $this->sut->shouldReceive('getRequest')
            ->andReturn($mockRequest)
            ->shouldReceive('getCrudAction')
            ->andReturn('add')
            ->shouldReceive('getIdentifier')
            ->andReturn($id)
            ->shouldReceive('isDiscPending')
            ->with($licenceVehicle)
            ->andReturn(true)
            ->shouldReceive('isDiscPending')
            ->with($licenceVehicle2)
            ->andReturn(false)
            ->shouldReceive('isDiscPending')
            ->with($licenceVehicle3)
            ->andReturn(false)
            ->shouldReceive('getLicenceId')
            ->andReturn($licenceId)
            ->shouldReceive('postSave')
            ->with('vehicles')
            ->shouldReceive('getActionFromCrudAction')
            ->with('add')
            ->andReturn('add')
            ->shouldReceive('checkForAlternativeCrudAction')
            ->with('add')
            ->andReturn('RESPONSE');

        $mockRequest->shouldReceive('isPost')
            ->andReturn(true)
            ->shouldReceive('getPost')
            ->with('query')
            ->andReturn($stubbedQuery)
            ->shouldReceive('getPost')
            ->andReturn($stubbedFormData)
            ->shouldReceive('getQuery')
            ->andReturn($stubbedQuery);

        $this->adapter
            ->shouldReceive('getFilteredVehiclesData')
            ->once()
            ->with(111, $stubbedQuery)
            ->andReturn($stubbedTableData)
            ->shouldReceive('alterVehcileTable')
            ->with($mockTable, $id)
            ->andReturn($mockTable);

        $mockFormService->shouldReceive('getForm')
            ->once()
            ->with($mockTable, true)
            ->andReturn($mockForm);

        $mockForm->shouldReceive('setData')
            ->once()
            ->with($stubbedFormData)
            ->andReturnSelf()
            ->shouldReceive('isValid')
            ->andReturn(true)
            ->shouldReceive('getData')
            ->andReturn(['form' => 'data']);

        $mockTableBuilder->shouldReceive('prepareTable')
            ->once()
            ->with('lva-vehicles', $preparedTableData, ['page' => 1, 'query' => ['page' => 1]])
            ->andReturn($mockTable);

        $mockBusinessService->shouldReceive('process')
            ->with(['id' => 111, 'data' => ['form' => 'data']])
            ->andReturn($mockResponse);

        $mockResponse->shouldReceive('isOk')
            ->andReturn(true);

        // Assertions
        $response = $this->sut->indexAction();

        $this->assertEquals('RESPONSE', $response);
    }

    public function testReprintActionGet()
    {
        // Mocks
        $mockRequest = m::mock();
        $mockForm = m::mock();

        $mockFormHelper = m::mock();
        $this->sm->setService('Helper\Form', $mockFormHelper);

        // Expectations
        $this->sut->shouldReceive('getRequest')
            ->andReturn($mockRequest)
            ->shouldReceive('render')
            ->with('reprint_vehicles', $mockForm)
            ->andReturn('RESPONSE');

        $mockRequest->shouldReceive('isPost')
            ->andReturn(false);

        $mockFormHelper->shouldReceive('createFormWithRequest')
            ->with('GenericConfirmation', $mockRequest)
            ->andReturn($mockForm);

        // Assertions
        $this->assertEquals('RESPONSE', $this->sut->reprintAction());
    }

    public function testReprintActionPost()
    {
        // Mocks
        $mockRequest = m::mock();

        $mockBusinessService = m::mock('\Common\BusinessService\BusinessServiceInterface');

        $bsm = m::mock('\Common\BusinessService\BusinessServiceManager')->makePartial();
        $bsm->setService('Lva\ReprintDisc', $mockBusinessService);

        $this->sm->setService('BusinessServiceManager', $bsm);

        // Expectations
        $this->sut->shouldReceive('getRequest')
            ->andReturn($mockRequest)
            ->shouldReceive('params')
            ->with('child_id')
            ->andReturn('11,22')
            ->shouldReceive('getIdentifierIndex')
            ->andReturn('licence')
            ->shouldReceive('getIdentifier')
            ->andReturn(111);

        $mockRequest->shouldReceive('isPost')
            ->andReturn(true);

        $mockBusinessService->shouldReceive('process')
            ->with(['ids' => [11, 22]]);

        $this->sut->shouldReceive('redirect->toRouteAjax')
            ->with(null, ['licence' => 111])
            ->andReturn('RESPONSE');

        // Assertions
        $this->assertEquals('RESPONSE', $this->sut->reprintAction());
    }

    /**
     * @dataProvider getDeleteMessageProvider
     */
    public function testGetDeleteMessage($params, $totalVehicles, $licence, $expected)
    {
        $licenceId = 1;

        // Set by Provider.

        $this->sut->shouldReceive('params')
            ->with('child_id')
            ->andReturn($params);

        $this->sut->shouldReceive('getLicenceId')
            ->andReturn($licenceId);

        $this->sm->setService(
            'Entity\Licence',
            m::mock()
                ->shouldReceive('getOverview')
                ->with($licenceId)
                ->andReturn($licence)
                ->getMock()
        );

        $this->sut->shouldReceive('getTotalNumberOfVehicles')
            ->andReturn($totalVehicles);

        $this->assertEquals($expected, $this->sut->getDeleteMessage());
    }

    public function getDeleteMessageProvider()
    {
        return array(
            array(
                '1',
                1,
                array(
                    'licenceType' => array(
                        'id' => 'ltyp_sn'
                    )
                ),
                'deleting.all.vehicles.message'
            ),
            array(
                '1,2',
                1,
                array(
                    'licenceType' => array(
                        'id' => 'ltyp_sn'
                    )
                ),
                'delete.confirmation.text'
            ),

            array(
                '1,2',
                1,
                array(
                    'licenceType' => array(
                        'id' => 'NotInAcceptedArray'
                    )
                ),
                'delete.confirmation.text'
            )
        );
    }
}
