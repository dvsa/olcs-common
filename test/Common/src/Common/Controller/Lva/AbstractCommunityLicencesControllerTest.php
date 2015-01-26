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

    public function testIndexActionWithPost()
    {
        // Mocks
        $mockRequest = m::mock();

        // Expectations
        $this->sut->shouldReceive('getRequest')
            ->andReturn($mockRequest);

        $mockRequest->shouldReceive('isPost')
            ->andReturn(true);

        $this->sut->shouldReceive('postSave')
            ->with('community_licences')
            ->shouldReceive('completeSection')
            ->with('community_licences')
            ->andReturn('REDIRECT');

        $this->assertEquals('REDIRECT', $this->sut->indexAction());
    }

    public function testIndexActionWithGetWithDefaultFilters()
    {
        // Data
        $id = 4;
        $stubbedIsFiltered = null;
        $stubbedFilters = [];
        $expectedFilters = [
            CommunityLicEntityService::STATUS_PENDING,
            CommunityLicEntityService::STATUS_VALID,
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

        // Mocks
        $mockRequest = m::mock();
        $mockFormHelper = m::mock();
        $this->sm->setService('Helper\Form', $mockFormHelper);
        $mockFilterForm = m::mock('\Zend\Form\Form');
        $mockForm = m::mock('\Zend\Form\Form');
        $mockTable = m::mock();
        $mockTableElement = m::mock();
        $mockTableBuilder = m::mock();
        $this->sm->setService('Table', $mockTableBuilder);
        $mockCommunityLicService = m::mock();
        $this->sm->setService('Entity\CommunityLic', $mockCommunityLicService);
        $mockScriptService = m::mock();
        $this->sm->setService('Script', $mockScriptService);

        // Expectations
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
            ->andReturn($mockTableElement);

        $mockTableBuilder->shouldReceive('prepareTable')
            ->with('lva-community-licences', $stubbedTableData)
            ->andReturn($mockTable);

        $mockCommunityLicService->shouldReceive('getList')
            ->with($expectedQuery)
            ->andReturn($stubbedTableData);

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
            ->shouldReceive('alterFormForLva')
            ->with($mockForm)
            ->shouldReceive('alterFormForLocation')
            ->with($mockForm)
            ->shouldReceive('render')
            ->with('community_licences', $mockForm, ['filterForm' => $mockFilterForm])
            ->andReturn('VIEW');

        $mockScriptService->shouldReceive('loadFile')
            ->with('forms/filter');

        $this->assertEquals('VIEW', $this->sut->indexAction());
    }

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

        // Mocks
        $mockRequest = m::mock();
        $mockFormHelper = m::mock();
        $this->sm->setService('Helper\Form', $mockFormHelper);
        $mockFilterForm = m::mock('\Zend\Form\Form');
        $mockForm = m::mock('\Zend\Form\Form');
        $mockTable = m::mock();
        $mockTableElement = m::mock();
        $mockTableBuilder = m::mock();
        $this->sm->setService('Table', $mockTableBuilder);
        $mockCommunityLicService = m::mock();
        $this->sm->setService('Entity\CommunityLic', $mockCommunityLicService);
        $mockScriptService = m::mock();
        $this->sm->setService('Script', $mockScriptService);

        // Expectations
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
            ->andReturn($mockTableElement);

        $mockTableBuilder->shouldReceive('prepareTable')
            ->with('lva-community-licences', $stubbedTableData)
            ->andReturn($mockTable);

        $mockCommunityLicService->shouldReceive('getList')
            ->with($expectedQuery)
            ->andReturn($stubbedTableData);

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
            ->shouldReceive('alterFormForLva')
            ->with($mockForm)
            ->shouldReceive('alterFormForLocation')
            ->with($mockForm)
            ->shouldReceive('render')
            ->with('community_licences', $mockForm, ['filterForm' => $mockFilterForm])
            ->andReturn('VIEW');

        $mockScriptService->shouldReceive('loadFile')
            ->with('forms/filter');

        $this->assertEquals('VIEW', $this->sut->indexAction());
    }

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

        // Mocks
        $mockRequest = m::mock();
        $mockFormHelper = m::mock();
        $this->sm->setService('Helper\Form', $mockFormHelper);
        $mockFilterForm = m::mock('\Zend\Form\Form');
        $mockForm = m::mock('\Zend\Form\Form');
        $mockTable = m::mock();
        $mockTableElement = m::mock();
        $mockTableBuilder = m::mock();
        $this->sm->setService('Table', $mockTableBuilder);
        $mockCommunityLicService = m::mock();
        $this->sm->setService('Entity\CommunityLic', $mockCommunityLicService);
        $mockScriptService = m::mock();
        $this->sm->setService('Script', $mockScriptService);

        // Expectations
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
            ->andReturn($mockTableElement);

        $mockTableBuilder->shouldReceive('prepareTable')
            ->with('lva-community-licences', $stubbedTableData)
            ->andReturn($mockTable);

        $mockCommunityLicService->shouldReceive('getList')
            ->with($expectedQuery)
            ->andReturn($stubbedTableData);

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
            ->shouldReceive('alterFormForLva')
            ->with($mockForm)
            ->shouldReceive('alterFormForLocation')
            ->with($mockForm)
            ->shouldReceive('render')
            ->with('community_licences', $mockForm, ['filterForm' => $mockFilterForm])
            ->andReturn('VIEW');

        $mockScriptService->shouldReceive('loadFile')
            ->with('forms/filter');

        $this->assertEquals('VIEW', $this->sut->indexAction());
    }
}
