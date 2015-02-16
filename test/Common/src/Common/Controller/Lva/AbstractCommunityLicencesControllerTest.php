<?php

namespace CommonTest\Controller\Lva;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use CommonTest\Bootstrap;
use Common\Service\Entity\CommunityLicEntityService;

/**
 * Test Abstract Community Licences Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class AbstractCommunityLicencesControllerTest extends MockeryTestCase
{
    protected $sut;
    protected $sm;

    public function setUp()
    {
        // Grab an empty service manager
        $this->sm = Bootstrap::getServiceManager();

        // Allows us to test the abstract
        $this->sut = m::mock('\Common\Controller\Lva\AbstractCommunityLicencesController')
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();
        $this->sut->setServiceLocator($this->sm);
    }

    /**
     * Test index action with post, no crud action
     * 
     * @group abstractCommunityLicenceController
     */
    public function testIndexActionWithPost()
    {
        // Mocks
        $mockRequest = m::mock()
            ->shouldReceive('isPost')
            ->andReturn(true)
            ->shouldReceive('getPost')
            ->andReturn(
                ['table' => []]
            )
            ->getMock();

        // Expectations
        $this->sut->shouldReceive('getRequest')
            ->andReturn($mockRequest);

        $this->sut
            ->shouldReceive('getCrudAction')
            ->with([[]])
            ->andReturn(null)
            ->shouldReceive('postSave')
            ->with('community_licences')
            ->shouldReceive('completeSection')
            ->with('community_licences')
            ->andReturn('REDIRECT');

        $this->assertEquals('REDIRECT', $this->sut->indexAction());
    }

    /**
     * Test index action with post, crud action
     * 
     * @group abstractCommunityLicenceController
     */
    public function testIndexActionWithPostCrudAction()
    {
        // Mocks
        $mockRequest = m::mock()
            ->shouldReceive('isPost')
            ->andReturn(true)
            ->shouldReceive('getPost')
            ->andReturn(
                ['table' => ['action' => 'add']]
            )
            ->getMock();

        // Expectations
        $this->sut->shouldReceive('getRequest')
            ->andReturn($mockRequest);

        $this->sut
            ->shouldReceive('getCrudAction')
            ->with([['action' => 'add']])
            ->andReturn('add')
            ->shouldReceive('handleCrudAction')
            ->with('add', ['add', 'office-licence-add'])
            ->andReturn('REDIRECT');

        $this->assertEquals('REDIRECT', $this->sut->indexAction());
    }

    /**
     * Test index action with get with default filters
     * 
     * @group abstractCommunityLicenceController
     */
    public function testIndexActionWithGetWithDefaultFilters()
    {
        // Data
        $id = 4;
        $stubbedIsFiltered = null;
        $stubbedFilters = [];
        $expectedFilters = [
            CommunityLicEntityService::STATUS_PENDING,
            CommunityLicEntityService::STATUS_ACTIVE,
            CommunityLicEntityService::STATUS_WITHDRAWN,
            CommunityLicEntityService::STATUS_SUSPENDED
        ];
        $expectedQuery = [
            'licence' => 4,
            'status' => $expectedFilters,
            'sort' => 'issueNo',
            'order' => 'DESC'
        ];
        $stubbedTableData = [
            'foo' => 'bar'
        ];
        $licenceData = [
            'totCommunityLicences' => 3
        ];
        $data = [
            'data' => [
                'totalCommunityLicences' => 3
            ]
        ];
        // Mocks
        $mockRequest = m::mock();
        $mockFormHelper = m::mock();
        $this->sm->setService('Helper\Form', $mockFormHelper);
        $mockFilterForm = m::mock('\Zend\Form\Form');
        $mockForm = m::mock('\Zend\Form\Form');
        $mockTableElement = m::mock();
        $mockTableBuilder = m::mock();
        $this->sm->setService('Table', $mockTableBuilder);
        $mockCommunityLicService = m::mock();
        $this->sm->setService('Entity\CommunityLic', $mockCommunityLicService);
        $mockScriptService = m::mock();
        $this->sm->setService('Script', $mockScriptService);

        // Expectations
        $mockLicenceService = m::mock()
            ->shouldReceive('getById')
            ->with($id)
            ->andReturn($licenceData)
            ->getMock();
        $this->sm->setService('Entity\Licence', $mockLicenceService);

        $mockTable = m::mock()
            ->shouldReceive('removeAction')
            ->with('office-licence-add')
            ->shouldReceive('getRows')
            ->andReturn([['status' => ['id' => CommunityLicEntityService::STATUS_ACTIVE]]])
            ->getMock();

        $mockRequest->shouldReceive('isPost')
            ->andReturn(false);

        $mockFormHelper->shouldReceive('createForm')
            ->with('Lva\CommunityLicenceFilter', false)
            ->andReturn($mockFilterForm)
            ->shouldReceive('createForm')
            ->with('Lva\CommunityLicences')
            ->andReturn($mockForm)
            ->shouldReceive('populateFormTable')
            ->with($mockTableElement, $mockTable);

        $mockFilterForm->shouldReceive('setData')
            ->with(['status' => $expectedFilters])
            ->andReturnSelf();

        $mockForm->shouldReceive('get')
            ->with('table')
            ->andReturn($mockTableElement)
            ->shouldReceive('setData')
            ->with($data);

        $mockTableBuilder->shouldReceive('prepareTable')
            ->with('lva-community-licences', $stubbedTableData)
            ->andReturn($mockTable);

        $mockCommunityLicService->shouldReceive('getList')
            ->with($expectedQuery)
            ->andReturn($stubbedTableData)
            ->shouldReceive('getOfficeCopy')
            ->with($id)
            ->andReturn(['officeCopy']);

        $this->sut->shouldReceive('params->fromQuery')
            ->with('status')
            ->andReturn($stubbedFilters);

        $this->sut->shouldReceive('params->fromQuery')
            ->with('isFiltered')
            ->andReturn($stubbedIsFiltered);

        $this->sut->shouldReceive('getRequest')
            ->andReturn($mockRequest)
            ->shouldReceive('getIdentifier')
            ->andReturn($id)
            ->shouldReceive('getLicenceId')
            ->andReturn($id)
            ->shouldReceive('alterFormForLva')
            ->with($mockForm)
            ->shouldReceive('alterFormForLocation')
            ->with($mockForm)
            ->shouldReceive('render')
            ->with('community_licences', $mockForm, ['filterForm' => $mockFilterForm])
            ->andReturn('VIEW');

        $mockScriptService->shouldReceive('loadFiles')
            ->with(['forms/filter', 'community-licence']);

        $this->assertEquals('VIEW', $this->sut->indexAction());
    }

    /**
     * Test index action with get with filters
     * 
     * @group abstractCommunityLicenceController
     */
    public function testIndexActionWithGetWithFilters()
    {
        // Data
        $id = 4;
        $stubbedIsFiltered = 1;
        $stubbedFilters = $expectedFilters = [
            CommunityLicEntityService::STATUS_PENDING
        ];
        $expectedQuery = [
            'licence' => 4,
            'status' => $expectedFilters,
            'sort' => 'issueNo',
            'order' => 'DESC'
        ];
        $stubbedTableData = [
            'foo' => 'bar'
        ];
        $licenceData = [
            'totCommunityLicences' => 3
        ];
        $data = [
            'data' => [
                'totalCommunityLicences' => 3
            ]
        ];

        // Mocks
        $mockRequest = m::mock();
        $mockFormHelper = m::mock();
        $this->sm->setService('Helper\Form', $mockFormHelper);
        $mockFilterForm = m::mock('\Zend\Form\Form');
        $mockForm = m::mock('\Zend\Form\Form');
        $mockTableElement = m::mock();
        $mockTableBuilder = m::mock();
        $this->sm->setService('Table', $mockTableBuilder);
        $mockCommunityLicService = m::mock();
        $this->sm->setService('Entity\CommunityLic', $mockCommunityLicService);
        $mockScriptService = m::mock();
        $this->sm->setService('Script', $mockScriptService);

        // Expectations
        $mockLicenceService = m::mock()
            ->shouldReceive('getById')
            ->with($id)
            ->andReturn($licenceData)
            ->getMock();
        $this->sm->setService('Entity\Licence', $mockLicenceService);

        $mockTable = m::mock()
            ->shouldReceive('removeAction')
            ->with('office-licence-add')
            ->once()
            ->shouldReceive('getRows')
            ->andReturn([])
            ->shouldReceive('removeAction')
            ->with('void')
            ->once()
            ->getMock();

        $mockRequest->shouldReceive('isPost')
            ->andReturn(false);

        $mockFormHelper->shouldReceive('createForm')
            ->with('Lva\CommunityLicenceFilter', false)
            ->andReturn($mockFilterForm)
            ->shouldReceive('createForm')
            ->with('Lva\CommunityLicences')
            ->andReturn($mockForm)
            ->shouldReceive('populateFormTable')
            ->with($mockTableElement, $mockTable);

        $mockFilterForm->shouldReceive('setData')
            ->with(['status' => $expectedFilters])
            ->andReturnSelf();

        $mockForm->shouldReceive('get')
            ->with('table')
            ->andReturn($mockTableElement)
            ->shouldReceive('setData')
            ->with($data);

        $mockTableBuilder->shouldReceive('prepareTable')
            ->with('lva-community-licences', $stubbedTableData)
            ->andReturn($mockTable);

        $mockCommunityLicService->shouldReceive('getList')
            ->with($expectedQuery)
            ->andReturn($stubbedTableData)
            ->shouldReceive('getOfficeCopy')
            ->with($id)
            ->andReturn(['officeCopy']);

        $this->sut->shouldReceive('params->fromQuery')
            ->with('status')
            ->andReturn($stubbedFilters);

        $this->sut->shouldReceive('params->fromQuery')
            ->with('isFiltered')
            ->andReturn($stubbedIsFiltered);

        $this->sut->shouldReceive('getRequest')
            ->andReturn($mockRequest)
            ->shouldReceive('getIdentifier')
            ->andReturn($id)
            ->shouldReceive('getLicenceId')
            ->andReturn($id)
            ->shouldReceive('alterFormForLva')
            ->with($mockForm)
            ->shouldReceive('alterFormForLocation')
            ->with($mockForm)
            ->shouldReceive('render')
            ->with('community_licences', $mockForm, ['filterForm' => $mockFilterForm])
            ->andReturn('VIEW');

        $mockScriptService->shouldReceive('loadFiles')
            ->with(['forms/filter', 'community-licence']);

        $this->assertEquals('VIEW', $this->sut->indexAction());
    }

    /**
     * Test index action with get with empty filters
     * 
     * @group abstractCommunityLicenceController
     */
    public function testIndexActionWithGetWithEmptyFilters()
    {
        // Data
        $id = 4;
        $stubbedIsFiltered = 1;
        $stubbedFilters = null;
        $expectedFilters = 'NULL';
        $expectedQuery = [
            'licence' => 4,
            'status' => $expectedFilters,
            'sort' => 'issueNo',
            'order' => 'DESC'
        ];
        $stubbedTableData = [
            'foo' => 'bar'
        ];
        $licenceData = [
            'totCommunityLicences' => 3
        ];
        $data = [
            'data' => [
                'totalCommunityLicences' => 3
            ]
        ];

        // Mocks
        $mockRequest = m::mock();
        $mockFormHelper = m::mock();
        $this->sm->setService('Helper\Form', $mockFormHelper);
        $mockFilterForm = m::mock('\Zend\Form\Form');
        $mockForm = m::mock('\Zend\Form\Form');
        $mockTableElement = m::mock();
        $mockTableBuilder = m::mock();
        $this->sm->setService('Table', $mockTableBuilder);
        $mockCommunityLicService = m::mock();
        $this->sm->setService('Entity\CommunityLic', $mockCommunityLicService);
        $mockScriptService = m::mock();
        $this->sm->setService('Script', $mockScriptService);

        // Expectations
        $mockLicenceService = m::mock()
            ->shouldReceive('getById')
            ->with($id)
            ->andReturn($licenceData)
            ->getMock();
        $this->sm->setService('Entity\Licence', $mockLicenceService);

        $mockTable = m::mock()
            ->shouldReceive('removeAction')
            ->with('office-licence-add')
            ->once()
            ->shouldReceive('getRows')
            ->andReturn([])
            ->shouldReceive('removeAction')
            ->with('void')
            ->once()
            ->getMock();

        $mockRequest->shouldReceive('isPost')
            ->andReturn(false);

        $mockFormHelper->shouldReceive('createForm')
            ->with('Lva\CommunityLicenceFilter', false)
            ->andReturn($mockFilterForm)
            ->shouldReceive('createForm')
            ->with('Lva\CommunityLicences')
            ->andReturn($mockForm)
            ->shouldReceive('populateFormTable')
            ->with($mockTableElement, $mockTable);

        $mockFilterForm->shouldReceive('setData')
            ->with(['status' => $expectedFilters])
            ->andReturnSelf();

        $mockForm->shouldReceive('get')
            ->with('table')
            ->andReturn($mockTableElement)
            ->shouldReceive('setData')
            ->with($data);

        $mockTableBuilder->shouldReceive('prepareTable')
            ->with('lva-community-licences', $stubbedTableData)
            ->andReturn($mockTable);

        $mockCommunityLicService->shouldReceive('getList')
            ->with($expectedQuery)
            ->andReturn($stubbedTableData)
            ->shouldReceive('getOfficeCopy')
            ->with($id)
            ->andReturn(['officeCopy']);

        $this->sut->shouldReceive('params->fromQuery')
            ->with('status')
            ->andReturn($stubbedFilters);

        $this->sut->shouldReceive('params->fromQuery')
            ->with('isFiltered')
            ->andReturn($stubbedIsFiltered);

        $this->sut->shouldReceive('getRequest')
            ->andReturn($mockRequest)
            ->shouldReceive('getIdentifier')
            ->andReturn($id)
            ->shouldReceive('getLicenceId')
            ->andReturn($id)
            ->shouldReceive('alterFormForLva')
            ->with($mockForm)
            ->shouldReceive('alterFormForLocation')
            ->with($mockForm)
            ->shouldReceive('render')
            ->with('community_licences', $mockForm, ['filterForm' => $mockFilterForm])
            ->andReturn('VIEW');

        $mockScriptService->shouldReceive('loadFiles')
            ->with(['forms/filter', 'community-licence']);

        $this->assertEquals('VIEW', $this->sut->indexAction());
    }

    /**
     * Test office licence add action
     * 
     * @group abstractCommunityLicenceController
     */
    public function testOfficeLicenceAddAction()
    {
        $mockTranslator = m::mock()
            ->shouldReceive('translate')
            ->with('internal.community_licence.office_copy_created')
            ->andReturn('message')
            ->getMock();

        $this->sm->setService('translator', $mockTranslator);

        $licenceId = 1;
        $this->sut
            ->shouldReceive('getAdapter')
            ->andReturn(
                m::mock()
                ->shouldReceive('addOfficeCopy')
                ->with($licenceId)
                ->getMock()
            )
            ->shouldReceive('getLicenceId')
            ->andReturn($licenceId)
            ->shouldReceive('addSuccessMessage')
            ->with('message')
            ->shouldReceive('redirect')
            ->andReturn(
                m::mock()
                ->shouldReceive('toRouteAjax')
                ->with('', ['action' => 'index', 'licence' => $licenceId])
                ->andReturn('redirect')
                ->getMock()
            )
            ->shouldReceive('getIdentifierIndex')
            ->andReturn('licence')
            ->shouldReceive('getIdentifier')
            ->andReturn($licenceId);

        $this->assertEquals('redirect', $this->sut->officeLicenceAddAction());
    }

    /**
     * Test add action
     * 
     * @group abstractCommunityLicenceController
     */
    public function testAddAction()
    {
        $licenceId = 1;
        $mockFormHelper = m::mock()
            ->shouldReceive('createForm')
            ->with('Lva\CommunityLicencesAdd')
            ->andReturn('form')
            ->getMock();

        $this->sm->setService('Helper\Form', $mockFormHelper);

        $this->sut
            ->shouldReceive('getRequest')
            ->andReturn(
                m::mock()
                ->shouldReceive('isPost')
                ->andReturn(false)
               ->getMock()
            )
            ->shouldReceive('getLicenceId')
            ->andReturn($licenceId)
            ->shouldReceive('render')
            ->andReturn('view');

        $this->assertEquals('view', $this->sut->addAction());
    }

    /**
     * Test add action with cancel button pressed
     * 
     * @group abstractCommunityLicenceController
     */
    public function testAddActionWithCancel()
    {
        $this->sut
            ->shouldReceive('getRequest')
            ->andReturn(
                m::mock()
                ->shouldReceive('isPost')
                ->andReturn(true)
                ->getMock()
            )
            ->shouldReceive('isButtonPressed')
            ->with('cancel')
            ->andReturn(true)
            ->shouldReceive('redirectToIndex')
            ->andReturn('redirect');

        $this->assertEquals('redirect', $this->sut->addAction());
    }

    /**
     * Test add action with post
     * 
     * @group abstractCommunityLicenceController
     */
    public function testAddActionWithPost()
    {
        $totalLicences = 2;
        $totalVehicleAuthority = 10;
        $licenceId = 1;
        $totalLicences = 3;
        $data = [
            'data' => [
                'total' => $totalLicences
            ]
        ];

        $mockRequest = m::mock()
            ->shouldReceive('isPost')
            ->andReturn(true)
            ->shouldReceive('getPost')
            ->andReturn($data)
            ->getMock();

        $mockValidator = m::mock()
            ->shouldReceive('setTotalLicences')
            ->with($totalLicences)
            ->shouldReceive('setTotalVehicleAuthority')
            ->with($totalVehicleAuthority)
            ->getMock();
        $this->sm->setService('totalVehicleAuthorityValidator', $mockValidator);

        $mockAdapter = m::mock()
            ->shouldReceive('getTotalAuthority')
            ->with('licence')
            ->andReturn($totalVehicleAuthority)
            ->shouldReceive('addOfficeCopy')
            ->with($licenceId)
            ->shouldReceive('addCommunityLicences')
            ->with($licenceId, $totalLicences)
            ->getMock();

        $mockLicenceService = m::mock()
            ->shouldReceive('updateCommunityLicencesCount')
            ->with($licenceId)
            ->getMock();
        $this->sm->setService('Entity\Licence', $mockLicenceService);

        $mockTranslator = m::mock()
            ->shouldReceive('translate')
            ->with('internal.community_licence.licences_created')
            ->andReturn('message')
            ->getMock();
        $this->sm->setService('translator', $mockTranslator);

        $mockForm = m::mock()
            ->shouldReceive('getInputFilter')
            ->andReturn(
                m::mock()
                ->shouldReceive('get')
                ->with('data')
                ->andReturn(
                    m::mock()
                    ->shouldReceive('get')
                    ->with('total')
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
            ->shouldReceive('setData')
            ->with($data)
            ->shouldReceive('isValid')
            ->andReturn(true)
            ->getMock();

        $mockFormHelper = m::mock()
            ->shouldReceive('createForm')
            ->with('Lva\CommunityLicencesAdd')
            ->andReturn($mockForm)
            ->getMock();
        $this->sm->setService('Helper\Form', $mockFormHelper);

        $mockCommunityLicService = m::mock()
            ->shouldReceive('getOfficeCopy')
            ->with($licenceId)
            ->andReturn(null)
            ->shouldReceive('getValidLicences')
            ->with($licenceId)
            ->andReturn(['Count' => $totalLicences])
            ->getMock();
        $this->sm->setService('Entity\CommunityLic', $mockCommunityLicService);

        $this->sut
            ->shouldReceive('getRequest')
            ->andReturn($mockRequest)
            ->shouldReceive('isButtonPressed')
            ->with('cancel')
            ->andReturn(false)
            ->shouldReceive('getLicenceId')
            ->andReturn($licenceId)
            ->shouldReceive('getIdentifier')
            ->andReturn('licence')
            ->shouldReceive('getAdapter')
            ->andReturn($mockAdapter)
            ->shouldReceive('addSuccessMessage')
            ->with('message')
            ->shouldReceive('redirectToIndex')
            ->andReturn('redirect');

        $this->assertEquals('redirect', $this->sut->addAction());
    }

    /**
     * Test add action with post, form is not valid
     * 
     * @group abstractCommunityLicenceController
     */
    public function testAddActionWithPostFormIsNotValid()
    {
        $totalLicences = 2;
        $totalVehicleAuthority = 10;
        $licenceId = 1;
        $totalLicences = 3;
        $data = [
            'data' => [
                'total' => $totalLicences
            ]
        ];

        $mockRequest = m::mock()
            ->shouldReceive('isPost')
            ->andReturn(true)
            ->shouldReceive('getPost')
            ->andReturn($data)
            ->getMock();

        $mockCommunityLicService = m::mock()
            ->shouldReceive('getValidLicences')
            ->with($licenceId)
            ->andReturn(['Count' => $totalLicences])
            ->getMock();
        $this->sm->setService('Entity\CommunityLic', $mockCommunityLicService);

        $mockValidator = m::mock()
            ->shouldReceive('setTotalLicences')
            ->with($totalLicences)
            ->shouldReceive('setTotalVehicleAuthority')
            ->with($totalVehicleAuthority)
            ->getMock();
        $this->sm->setService('totalVehicleAuthorityValidator', $mockValidator);

        $mockAdapter = m::mock()
            ->shouldReceive('getTotalAuthority')
            ->with('licence')
            ->andReturn($totalVehicleAuthority)
            ->getMock();

        $mockForm = m::mock()
            ->shouldReceive('getInputFilter')
            ->andReturn(
                m::mock()
                ->shouldReceive('get')
                ->with('data')
                ->andReturn(
                    m::mock()
                    ->shouldReceive('get')
                    ->with('total')
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
            ->shouldReceive('setData')
            ->with($data)
            ->shouldReceive('isValid')
            ->andReturn(false)
            ->getMock();

        $mockFormHelper = m::mock()
            ->shouldReceive('createForm')
            ->with('Lva\CommunityLicencesAdd')
            ->andReturn($mockForm)
            ->getMock();
        $this->sm->setService('Helper\Form', $mockFormHelper);

        $this->sut
            ->shouldReceive('getRequest')
            ->andReturn($mockRequest)
            ->shouldReceive('isButtonPressed')
            ->with('cancel')
            ->andReturn(false)
            ->shouldReceive('getLicenceId')
            ->andReturn($licenceId)
            ->shouldReceive('getIdentifier')
            ->andReturn('licence')
            ->shouldReceive('getAdapter')
            ->andReturn($mockAdapter)
            ->shouldReceive('render')
            ->andReturn('view');

        $this->assertEquals('view', $this->sut->addAction());
    }

    /**
     * Test void action not allowed
     * 
     * @group abstractCommunityLicenceController
     */
    public function testVoidActionNotAllowed()
    {
        $licenceId = 1;
        $licences = [1, 2];

        $mockTranslator = m::mock()
            ->shouldReceive('translate')
            ->with('internal.community_licence.not_allowed')
            ->andReturn('message')
            ->getMock();
        $this->sm->setService('translator', $mockTranslator);

        $mockLicenceService = m::mock()
            ->shouldReceive('getCommunityLicencesByLicenceIdAndIds')
            ->with($licenceId, $licences)
            ->andReturn([['issueNo' => 0]])
            ->getMock();
        $this->sm->setService('Entity\Licence', $mockLicenceService);

        $mockCommunityLicService = m::mock()
            ->shouldReceive('getValidLicences')
            ->with($licenceId)
            ->andReturn(['Results' => [['id' => 999]]])
            ->getMock();
        $this->sm->setService('Entity\CommunityLic', $mockCommunityLicService);

        $this->sut
            ->shouldReceive('getRequest')
            ->andReturn(m::mock())
            ->shouldReceive('params')
            ->with('child_id')
            ->andReturn('1,2')
            ->shouldReceive('getLicenceId')
            ->andReturn($licenceId)
            ->shouldReceive('addErrorMessage')
            ->with('message')
            ->shouldReceive('redirectToIndex')
            ->andReturn('redirect');

        $this->assertEquals('redirect', $this->sut->voidAction());
    }

    /**
     * Test void action with display wuesiton
     * 
     * @group abstractCommunityLicenceController
     */
    public function testVoidActionWithDisplayQuestion()
    {
        $licenceId = 1;
        $licences = [1, 2];

        $mockTranslator = m::mock()
            ->shouldReceive('translate')
            ->with('internal.community_licence.not_allowed')
            ->andReturn('message')
            ->getMock();
        $this->sm->setService('translator', $mockTranslator);

        $mockLicenceService = m::mock()
            ->shouldReceive('getCommunityLicencesByLicenceIdAndIds')
            ->with($licenceId, $licences)
            ->andReturn([['issueNo' => 0]])
            ->getMock();
        $this->sm->setService('Entity\Licence', $mockLicenceService);

        $mockCommunityLicService = m::mock()
            ->shouldReceive('getValidLicences')
            ->with($licenceId)
            ->andReturn(['Results' => [['id' => 1], ['id' => 2]]])
            ->getMock();
        $this->sm->setService('Entity\CommunityLic', $mockCommunityLicService);

        $mockFormHelper = m::mock()
            ->shouldReceive('createForm')
            ->with('Lva\CommunityLicencesVoid')
            ->andReturn('form')
            ->getMock();
        $this->sm->setService('Helper\Form', $mockFormHelper);

        $this->sut
            ->shouldReceive('getRequest')
            ->andReturn(
                m::mock()
                ->shouldReceive('isPost')
                ->andReturn(false)
                ->getMock()
            )
            ->shouldReceive('params')
            ->with('child_id')
            ->andReturn('1,2')
            ->shouldReceive('getLicenceId')
            ->andReturn($licenceId)
            ->shouldReceive('render')
            ->andReturn('view');

        $this->assertEquals('view', $this->sut->voidAction());
    }

    /**
     * Test void action with cancel
     * 
     * @group abstractCommunityLicenceController
     */
    public function testVoidActionWithCancel()
    {
        $licenceId = 1;
        $licences = [1, 2];

        $mockTranslator = m::mock()
            ->shouldReceive('translate')
            ->with('internal.community_licence.not_allowed')
            ->andReturn('message')
            ->getMock();
        $this->sm->setService('translator', $mockTranslator);

        $mockLicenceService = m::mock()
            ->shouldReceive('getCommunityLicencesByLicenceIdAndIds')
            ->with($licenceId, $licences)
            ->andReturn([['issueNo' => 0]])
            ->getMock();
        $this->sm->setService('Entity\Licence', $mockLicenceService);

        $mockCommunityLicService = m::mock()
            ->shouldReceive('getValidLicences')
            ->with($licenceId)
            ->andReturn(['Results' => [['id' => 1], ['id' => 2]]])
            ->getMock();
        $this->sm->setService('Entity\CommunityLic', $mockCommunityLicService);

        $this->sut
            ->shouldReceive('getRequest')
            ->andReturn(
                m::mock()
                ->shouldReceive('isPost')
                ->andReturn(true)
                ->getMock()
            )
            ->shouldReceive('params')
            ->with('child_id')
            ->andReturn('1,2')
            ->shouldReceive('getLicenceId')
            ->andReturn($licenceId)
            ->shouldReceive('isButtonPressed')
            ->with('cancel')
            ->andReturn(true)
            ->shouldReceive('redirectToIndex')
            ->andReturn('redirect');

        $this->assertEquals('redirect', $this->sut->voidAction());
    }

    /**
     * Test void action
     * 
     * @group abstractCommunityLicenceController
     */
    public function testVoidAction()
    {
        $licenceId = 1;
        $licences = [1, 2];

        $mockTranslator = m::mock()
            ->shouldReceive('translate')
            ->with('internal.community_licence.licences_voided')
            ->andReturn('message')
            ->getMock();
        $this->sm->setService('translator', $mockTranslator);

        $mockLicenceService = m::mock()
            ->shouldReceive('getCommunityLicencesByLicenceIdAndIds')
            ->with($licenceId, $licences)
            ->andReturn([['issueNo' => 0]])
            ->shouldReceive('updateCommunityLicencesCount')
            ->with($licenceId)
            ->getMock();
        $this->sm->setService('Entity\Licence', $mockLicenceService);

        $mockCommunityLicService = m::mock()
            ->shouldReceive('getValidLicences')
            ->with($licenceId)
            ->andReturn(['Results' => [['id' => 1], ['id' => 2]]])
            ->shouldReceive('multiUpdate')
            ->with(
                [
                    [
                        'status' => CommunityLicEntityService::STATUS_VOID,
                        'expiredDate' => '2015-01-01',
                        'licence' => $licenceId,
                        'issueNo' => 0
                    ]
                ]
            )
            ->getMock();
        $this->sm->setService('Entity\CommunityLic', $mockCommunityLicService);

        $mockDateHelper = m::mock()
            ->shouldReceive('getDate')
            ->andReturn('2015-01-01')
            ->getMock();
        $this->sm->setService('Helper\Date', $mockDateHelper);

        $this->sut
            ->shouldReceive('getRequest')
            ->andReturn(
                m::mock()
                ->shouldReceive('isPost')
                ->andReturn(true)
                ->getMock()
            )
            ->shouldReceive('params')
            ->with('child_id')
            ->andReturn('1,2')
            ->shouldReceive('getLicenceId')
            ->andReturn($licenceId)
            ->shouldReceive('isButtonPressed')
            ->with('cancel')
            ->andReturn(false)
            ->shouldReceive('addSuccessMessage')
            ->with('message')
            ->shouldReceive('redirectToIndex')
            ->andReturn('redirect');

        $this->assertEquals('redirect', $this->sut->voidAction());
    }
}
