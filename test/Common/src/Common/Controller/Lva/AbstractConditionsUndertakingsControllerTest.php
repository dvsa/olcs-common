<?php

/**
 * Test Abstract Conditions & Undertakings Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Controller\Lva;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use CommonTest\Bootstrap;

/**
 * Test Abstract Conditions & Undertakings Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class AbstractConditionsUndertakingsControllerTest extends MockeryTestCase
{
    protected $sut;
    protected $sm;
    protected $adapter;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();
        $this->adapter = m::mock('\Common\Controller\Lva\Interfaces\AdapterInterface');

        $this->sut = m::mock('\Common\Controller\Lva\AbstractConditionsUndertakingsController')
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $this->sut->setServiceLocator($this->sm);
        $this->sut->setAdapter($this->adapter);
    }

    public function testIndexActionWithGet()
    {
        // Data
        $stubbedTableData = [
            'foo' => 'bar'
        ];

        // Mocks
        $request = m::mock();
        $mockForm = m::mock('\Zend\Form\Form');
        $mockTableFieldset = m::mock();
        $mockTable = m::mock();
        $mockFormHelper = m::mock();
        $this->sm->setService('Helper\Form', $mockFormHelper);
        $mockTableBuilder = m::mock();
        $this->sm->setService('Table', $mockTableBuilder);
        $mockScript = m::mock();
        $this->sm->setService('Script', $mockScript);

        // Expectations
        $mockScript->shouldReceive('loadFile')
            ->with('lva-crud');

        $this->sut->shouldReceive('getRequest')
            ->andReturn($request)
            ->shouldReceive('getIdentifier')
            ->andReturn(7)
            ->shouldReceive('alterFormForLva')
            ->with($mockForm)
            ->shouldReceive('render')
            ->with('conditions_undertakings', $mockForm)
            ->andReturn('RENDER');

        $request->shouldReceive('isPost')
            ->andReturn(false);

        $mockForm->shouldReceive('get')
            ->with('table')
            ->andReturn($mockTableFieldset);

        $mockTableBuilder->shouldReceive('prepareTable')
            ->with('lva-conditions-undertakings', $stubbedTableData)
            ->andReturn($mockTable);

        $this->adapter->shouldReceive('getTableData')
            ->with(7)
            ->andReturn($stubbedTableData)
            ->shouldReceive('alterTable')
            ->with($mockTable)
            ->shouldReceive('getTableName')
            ->andReturn('lva-conditions-undertakings')
            ->shouldReceive('attachMainScripts');

        $mockFormHelper->shouldReceive('createForm')
            ->with('Lva\ConditionsUndertakings')
            ->andReturn($mockForm)
            ->shouldReceive('populateFormTable')
            ->with($mockTableFieldset, $mockTable);

        $this->assertEquals('RENDER', $this->sut->indexAction());
    }

    public function testIndexActionWithPost()
    {
        // Data
        $postData = [
            'table' => 'bar'
        ];

        // Mocks
        $request = m::mock();

        // Expectations
        $this->sut->shouldReceive('getRequest')
            ->andReturn($request)
            ->shouldReceive('getCrudAction')
            ->andReturn(null)
            ->shouldReceive('postSave')
            ->with('conditions_undertakings')
            ->shouldReceive('completeSection')
            ->with('conditions_undertakings')
            ->andReturn('REDIRECT');

        $request->shouldReceive('isPost')
            ->andReturn(true)
            ->shouldReceive('getPost')
            ->andReturn($postData);

        $this->assertEquals('REDIRECT', $this->sut->indexAction());
    }

    public function testIndexActionWithPostCrudAction()
    {
        // Data
        $postData = [
            'table' => 'bar'
        ];

        // Mocks
        $request = m::mock();

        // Expectations
        $this->sut->shouldReceive('getRequest')
            ->andReturn($request)
            ->shouldReceive('getCrudAction')
            ->andReturn('crud')
            ->shouldReceive('handleCrudAction')
            ->with('crud')
            ->andReturn('REDIRECT');

        $request->shouldReceive('isPost')
            ->andReturn(true)
            ->shouldReceive('getPost')
            ->andReturn($postData);

        $this->assertEquals('REDIRECT', $this->sut->indexAction());
    }

    public function testAddActionWithGet()
    {
        // Mocks
        $request = m::mock();
        $mockForm = m::mock();
        $mockFormHelper = m::mock();
        $this->sm->setService('Helper\Form', $mockFormHelper);

        // Expectations
        $this->sut->shouldReceive('getRequest')
            ->andReturn($request)
            ->shouldReceive('getIdentifier')
            ->andReturn(7)
            ->shouldReceive('render')
            ->with('add_condition_undertaking', $mockForm)
            ->andReturn('RENDER')
            ->shouldReceive('params')
            ->with('child_id')
            ->andReturn(3);

        $request->shouldReceive('isPost')
            ->andReturn(false);

        $mockFormHelper->shouldReceive('createForm')
            ->with('ConditionUndertakingForm')
            ->andReturn($mockForm);

        $this->adapter->shouldReceive('alterForm')
            ->with($mockForm, 7)
            ->shouldReceive('canEditRecord')
            ->with(3, 7)
            ->andReturn(true);

        $mockForm->shouldReceive('setData')
            ->with([]);

        $this->assertEquals('RENDER', $this->sut->addAction());
    }

    public function testEditActionWithGet()
    {
        // Data
        $stubbedData = [
            'foo' => 'bar',
            'bar' => [
                'id' => 1,
                'cake' => 'foo'
            ]
        ];
        $expectedData = [
            'foo' => 'bar',
            'bar' => 1
        ];

        // Mocks
        $request = m::mock();
        $mockForm = m::mock();
        $mockEntityService = m::mock();
        $this->sm->setService('Entity\ConditionUndertaking', $mockEntityService);
        $mockFormHelper = m::mock();
        $this->sm->setService('Helper\Form', $mockFormHelper);

        // Expectations
        $this->sut->shouldReceive('getRequest')
            ->andReturn($request)
            ->shouldReceive('params')
            ->with('child_id')
            ->andReturn(3)
            ->shouldReceive('getIdentifier')
            ->andReturn(7)
            ->shouldReceive('render')
            ->with('edit_condition_undertaking', $mockForm)
            ->andReturn('RENDER');

        $request->shouldReceive('isPost')
            ->andReturn(false);

        $mockEntityService->shouldReceive('getCondition')
            ->with(3)
            ->andReturn($stubbedData);

        $mockFormHelper->shouldReceive('createForm')
            ->with('ConditionUndertakingForm')
            ->andReturn($mockForm);

        $this->adapter->shouldReceive('alterForm')
            ->with($mockForm, 7)
            ->shouldReceive('processDataForForm')
            ->with(['fields' => $expectedData])
            ->andReturn($expectedData)
            ->shouldReceive('canEditRecord')
            ->with(3, 7)
            ->andReturn(true);

        $mockForm->shouldReceive('setData')
            ->with($expectedData);

        $this->assertEquals('RENDER', $this->sut->editAction());
    }

    public function testEditActionWithGetCantEdit()
    {
        // Mocks
        $request = m::mock();
        $mockFlashMessenger = m::mock();
        $this->sm->setService('Helper\FlashMessenger', $mockFlashMessenger);

        // Expectations
        $this->sut->shouldReceive('getRequest')
            ->andReturn($request)
            ->shouldReceive('params')
            ->with('child_id')
            ->andReturn(3)
            ->shouldReceive('getIdentifier')
            ->andReturn(7);

        $this->sut->shouldReceive('redirect->toRouteAjax')
            ->with(null, ['action' => null], [], true)
            ->andReturn('REDIRECT');

        $request->shouldReceive('isPost')
            ->andReturn(false);

        $this->adapter->shouldReceive('canEditRecord')
            ->with(3, 7)
            ->andReturn(false);

        $mockFlashMessenger->shouldReceive('addErrorMessage')
            ->with('generic-cant-edit-message');

        $this->assertEquals('REDIRECT', $this->sut->editAction());
    }

    public function testAddActionWithPost()
    {
        // Data
        $postData = [
            'foo' => 'bar'
        ];
        $stubbedData = [
            'fields' => ['foo' => 'cake']
        ];

        // Mocks
        $request = m::mock();
        $mockForm = m::mock();
        $mockFormHelper = m::mock();
        $this->sm->setService('Helper\Form', $mockFormHelper);

        // Expectations
        $this->sut->shouldReceive('getRequest')
            ->andReturn($request)
            ->shouldReceive('params')
            ->with('child_id')
            ->andReturn(3)
            ->shouldReceive('getIdentifier')
            ->andReturn(7)
            ->shouldReceive('handlePostSave')
            ->andReturn('REDIRECT');

        $request->shouldReceive('isPost')
            ->andReturn(true)
            ->shouldReceive('getPost')
            ->andReturn($postData);

        $mockFormHelper->shouldReceive('createForm')
            ->with('ConditionUndertakingForm')
            ->andReturn($mockForm);

        $this->adapter->shouldReceive('alterForm')
            ->with($mockForm, 7)
            ->shouldReceive('processDataForSave')
            ->with($postData, 7)
            ->andReturn($stubbedData)
            ->shouldReceive('save')
            ->with(['foo' => 'cake'])
            ->shouldReceive('canEditRecord')
            ->with(3, 7)
            ->andReturn(true);

        $mockForm->shouldReceive('setData')
            ->with($postData)
            ->shouldReceive('isValid')
            ->andReturn(true);

        $this->assertEquals('REDIRECT', $this->sut->addAction());
    }

    public function testAddActionWithPostNotValid()
    {
        // Data
        $postData = [
            'foo' => 'bar'
        ];
        $stubbedData = [
            'bar' => 'cake'
        ];

        // Mocks
        $request = m::mock();
        $mockForm = m::mock();
        $mockFormHelper = m::mock();
        $this->sm->setService('Helper\Form', $mockFormHelper);

        // Expectations
        $this->sut->shouldReceive('getRequest')
            ->andReturn($request)
            ->shouldReceive('params')
            ->with('child_id')
            ->andReturn(3)
            ->shouldReceive('getIdentifier')
            ->andReturn(7)
            ->shouldReceive('render')
            ->with('add_condition_undertaking', $mockForm)
            ->andReturn('RENDER');

        $request->shouldReceive('isPost')
            ->andReturn(true)
            ->shouldReceive('getPost')
            ->andReturn($postData);

        $mockFormHelper->shouldReceive('createForm')
            ->with('ConditionUndertakingForm')
            ->andReturn($mockForm);

        $this->adapter->shouldReceive('alterForm')
            ->with($mockForm, 7)
            ->shouldReceive('processDataForForm')
            ->with(['fields' => $stubbedData])
            ->andReturn($stubbedData)
            ->shouldReceive('canEditRecord')
            ->with(3, 7)
            ->andReturn(true);

        $mockForm->shouldReceive('setData')
            ->with($postData)
            ->shouldReceive('isValid')
            ->andReturn(false);

        $this->assertEquals('RENDER', $this->sut->addAction());
    }

    public function testDeleteAction()
    {
        // Data
        $childId = '1,2,3';

        // Mock
        $request = m::mock();
        $mockEntityService = m::mock();
        $this->sm->setService('Entity\ConditionUndertaking', $mockEntityService);

        // Expectations
        $this->sut->shouldReceive('getRequest')
            ->andReturn($request)
            ->shouldReceive('params')
            ->with('child_id')
            ->andReturn($childId)
            ->shouldReceive('postSave')
            ->with('condition')
            ->shouldReceive('getIdentifier')
            ->andReturn(1);

        $this->sut->shouldReceive('redirect->toRouteAjax')
            ->with(null, ['application' => 1])
            ->andReturn('REDIRECT');

        $this->adapter->shouldReceive('delete')
            ->with(1, 1)
            ->shouldReceive('delete')
            ->with(2, 1)
            ->shouldReceive('delete')
            ->with(3, 1);

        $request->shouldReceive('isPost')
            ->andReturn(true);

        $this->assertEquals('REDIRECT', $this->sut->deleteAction());
    }
}
