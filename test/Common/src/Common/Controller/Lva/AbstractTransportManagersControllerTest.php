<?php

namespace CommonTest\Controller\Lva;

use \Mockery as m;

/**
 * Test Abstract Transport Managers Controller
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class AbstractTransportManagersControllerTest extends AbstractLvaControllerTestCase
{
    private $adapter;

    public function setUp()
    {
        parent::setUp();

        $this->mockController('\Common\Controller\Lva\AbstractTransportManagersController');

        $this->adapter = m::mock('\Common\Controller\Lva\Interfaces\AdapterInterface');
        $this->sut->setAdapter($this->adapter);

    }

    protected function setupIndex()
    {
        $this->sut->shouldReceive('getIdentifier')->andReturn(121);

        $mockTable = m::mock('StdClass');
        $mockTable->shouldReceive('loadData')->once()->with(['row1', 'row2']);
        $mockTable->shouldReceive('getRows')->andReturn(['row1', 'row2']);

        $mockTableElement = m::mock('StdClass');
        $mockTableElement->shouldReceive('setTable')->with($mockTable);
        $mockRowElement = m::mock('StdClass');
        $mockRowElement->shouldReceive('setValue')->with(2);

        $mockForm = $this->createMockForm('Lva\TransportManagers');
        $mockForm->shouldReceive('get->get')->once()->with('table')->andReturn($mockTableElement);
        $mockForm->shouldReceive('get->get')->once()->with('rows')->andReturn($mockRowElement);

        $this->adapter->shouldReceive('getForm')->once()->andReturn($mockForm);
        $this->adapter->shouldReceive('getTable')->once()->andReturn($mockTable);
        $this->adapter->shouldReceive('getTableData')->once()->with(121)->andReturn(['row1', 'row2']);

        $this->mockForm = $mockForm;
        $this->mockTable = $mockTable;
    }

    public function testGetIndexAction()
    {
        $this->setupIndex();

        $this->mockRender();

        $this->sm->shouldReceive('get->loadFile')->once()->with('lva-crud');

        $this->sut->indexAction();

        $this->assertEquals('transport_managers', $this->view);
    }

    public function testIndexPostWithValidData()
    {
        $postData = [
            'table' => 'foo',
            'foo' => 'bar'
        ];

        $this->setupIndex();

        $this->setPost($postData);

        $this->mockForm->shouldReceive('setData')->once()->with($postData);
        $this->adapter->shouldReceive('mustHaveAtLeastOneTm')->once()->andReturn(false);
        $this->mockForm->shouldReceive('getInputFilter->remove')->once()->with('table');
        $this->mockForm->shouldReceive('isValid')->once()->andReturn(true);
        $this->sut->shouldReceive('postSave')->with('transport_managers');
        $this->sut->shouldReceive('completeSection')->with('transport_managers')->andReturn('complete');

        $this->assertEquals('complete', $this->sut->indexAction());
    }

    public function testIndexPostTableValidatorRemoved()
    {
        $postData = [
            'table' => 'foo',
            'foo' => 'bar'
        ];

        $this->setupIndex();

        $this->setPost($postData);

        $this->mockForm->shouldReceive('setData')->once()->with($postData);
        $this->adapter->shouldReceive('mustHaveAtLeastOneTm')->once()->andReturn(true);
        $this->mockForm->shouldNotReceive('getInputFilter->remove');
        $this->mockForm->shouldReceive('isValid')->once()->andReturn(true);
        $this->sut->shouldReceive('postSave')->with('transport_managers');
        $this->sut->shouldReceive('completeSection')->with('transport_managers')->andReturn('complete');

        $this->assertEquals('complete', $this->sut->indexAction());
    }

    public function testIndexPostFailValidation()
    {
        $postData = [
            'table' => 'foo',
            'foo' => 'bar'
        ];

        $this->setupIndex();

        $this->setPost($postData);

        $this->mockForm->shouldReceive('setData')->once()->with($postData);
        $this->adapter->shouldReceive('mustHaveAtLeastOneTm')->once()->andReturn(true);
        $this->mockForm->shouldReceive('isValid')->once()->andReturn(false);

        $this->sm->shouldReceive('get->loadFile')->once()->with('lva-crud');

        $this->mockRender();

        $this->sut->indexAction();
        $this->assertEquals('transport_managers', $this->view);
    }

    public function testIndexPostCrud()
    {
        $postData = [
            'table' => [
                'action' => 'delete'
            ],
            'foo' => 'bar'
        ];

        $this->setupIndex();

        $this->setPost($postData);

        $this->mockForm->shouldReceive('setData')->once()->with($postData);
        $this->adapter->shouldReceive('mustHaveAtLeastOneTm')->once()->andReturn(true);
        $this->mockForm->shouldNotReceive('getInputFilter->remove');
        $this->sut->shouldReceive('handleCrudAction')->once()->with(['action' => 'delete'])->andReturn('CRUD');

        $this->assertEquals('CRUD', $this->sut->indexAction());
    }

    public function testDelete()
    {
        $mockBusinessService = m::mock('StdClass');
        $this->sut->shouldReceive('params')->once()->with('child_id')->andReturn('4,7,5,234');
        $this->sm->shouldReceive('get->get')->once()->andReturn($mockBusinessService);

        $mockBusinessService->shouldReceive('process')->once()->with(['ids' => [4,7,5,234]]);

        $this->sut->delete();
    }

    public function testGetDeleteMessage()
    {
        $this->assertEquals('review-transport_managers_delete', $this->sut->getDeleteMessage());
    }

    public function testGetDeleteTitle()
    {
        $this->assertEquals('delete-tm', $this->sut->getDeleteTitle());
    }
}
