<?php

namespace CommonTest\Controller\Lva;

use Mockery as m;

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
        $this->sut->shouldReceive('getLicenceId')->andReturn(765);

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

        $mockFormService = m::mock();
        $this->sm->shouldReceive('get')->once()->with('FormServiceManager')->andReturn($mockFormService);
        $mockFormService->shouldReceive('get->alterForm')->once()->with($mockForm);

        $this->adapter->shouldReceive('getForm')->once()->andReturn($mockForm);
        $this->adapter->shouldReceive('getTable')->once()->andReturn($mockTable);
        $this->adapter->shouldReceive('getTableData')->once()->with(121, 765)->andReturn(['row1', 'row2']);
        $this->adapter->shouldReceive('addMessages')->once()->with(765);

        $this->mockForm = $mockForm;
        $this->mockTable = $mockTable;
    }

    public function testGetIndexAction()
    {
        $this->setupIndex();

        $this->mockRender();

        $mockScript = m::mock();
        $this->sm->shouldReceive('get')->once()->with('Script')->andReturn($mockScript);
        $mockScript->shouldReceive('loadFile')->once()->with('lva-crud-delta');

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

        $mockScript = m::mock();
        $this->sm->shouldReceive('get')->once()->with('Script')->andReturn($mockScript);
        $mockScript->shouldReceive('loadFile')->once()->with('lva-crud-delta');

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
        $this->sut->shouldReceive('params')->once()->with('child_id')->andReturn('4,7,5,234');
        $this->sut->shouldReceive('getIdentifier')->once()->with()->andReturn('87');

        $this->adapter->shouldReceive('delete')->once()->with([4, 7, 5, 234], 87);

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

    public function testAddActionGet()
    {
        $this->markTestIncomplete();

        $registeredUsers = [
            'foo' => 'bar'
        ];

        // Mocks
        $mockRegisteredUser = m::mock();
        $mockForm = $this->createMockForm('Lva\AddTransportManager');

        $mockOrganisation = m::mock();
        $this->sm->setService('Entity\Organisation', $mockOrganisation);

        // Expectations
        $this->sut->shouldReceive('getCurrentOrganisationId')
            ->andReturn(1)
            ->shouldReceive('render')
            ->with('add-transport_managers', $mockForm)
            ->andReturn('RESPONSE');

        $mockOrganisation->shouldReceive('getRegisteredUsersForSelect')
            ->once()
            ->with(1)
            ->andReturn($registeredUsers);

        $mockForm->shouldReceive('get->get')
            ->with('registeredUser')
            ->andReturn($mockRegisteredUser);

        $mockRegisteredUser->shouldReceive('setEmptyOption')
            ->with('Please select')
            ->shouldReceive('setValueOptions')
            ->with($registeredUsers);

        $this->assertEquals('RESPONSE', $this->sut->addAction());
    }

    public function testAddActionPostFail()
    {
        $this->markTestIncomplete();

        $registeredUsers = [
            'foo' => 'bar'
        ];

        $postData = [
            'cake' => 'bar'
        ];

        $this->setPost($postData);

        // Mocks
        $mockRegisteredUser = m::mock();
        $mockForm = $this->createMockForm('Lva\AddTransportManager');

        $mockOrganisation = m::mock();
        $this->sm->setService('Entity\Organisation', $mockOrganisation);

        // Expectations
        $this->sut->shouldReceive('getCurrentOrganisationId')
            ->andReturn(1)
            ->shouldReceive('render')
            ->with('add-transport_managers', $mockForm)
            ->andReturn('RESPONSE');

        $mockOrganisation->shouldReceive('getRegisteredUsersForSelect')
            ->once()
            ->with(1)
            ->andReturn($registeredUsers);

        $mockForm->shouldReceive('get->get')
            ->with('registeredUser')
            ->andReturn($mockRegisteredUser);

        $mockRegisteredUser->shouldReceive('setEmptyOption')
            ->with('Please select')
            ->shouldReceive('setValueOptions')
            ->with($registeredUsers);

        $mockForm->shouldReceive('setData')
            ->with($postData)
            ->shouldReceive('isValid')
            ->andReturn(false);

        $this->assertEquals('RESPONSE', $this->sut->addAction());
    }

    public function testAddActionPostSuccess()
    {
        $this->markTestIncomplete();

        $registeredUsers = [
            'foo' => 'bar'
        ];

        $postData = [
            'data' => [
                'registeredUser' => 111
            ]
        ];

        $this->setPost($postData);

        // Mocks
        $mockRegisteredUser = m::mock();
        $mockForm = $this->createMockForm('Lva\AddTransportManager');

        $mockOrganisation = m::mock();
        $this->sm->setService('Entity\Organisation', $mockOrganisation);

        // Expectations
        $this->sut->shouldReceive('getCurrentOrganisationId')
            ->andReturn(1);

        $mockOrganisation->shouldReceive('getRegisteredUsersForSelect')
            ->once()
            ->with(1)
            ->andReturn($registeredUsers);

        $mockForm->shouldReceive('get->get')
            ->with('registeredUser')
            ->andReturn($mockRegisteredUser);

        $mockRegisteredUser->shouldReceive('setEmptyOption')
            ->with('Please select')
            ->shouldReceive('setValueOptions')
            ->with($registeredUsers);

        $mockForm->shouldReceive('setData')
            ->with($postData)
            ->shouldReceive('isValid')
            ->andReturn(true);

        $this->sut->shouldReceive('redirect->toRoute')
            ->with(null, ['action' => 'addTm', 'child_id' => 111], [], true)
            ->andReturn('RESPONSE');

        $this->assertEquals('RESPONSE', $this->sut->addAction());
    }

    public function testAddTmActionWithSameUser()
    {
        $this->markTestIncomplete();

        $user = [
            'id' => 111
        ];

        // Mocks
        $bsm = m::mock('\Common\BusinessService\BusinessServiceManager')->makePartial();
        $this->sm->setService('BusinessServiceManager', $bsm);

        $mockTma = m::mock('\Common\BusinessService\BusinessServiceInterface');
        $bsm->setService('Lva\TransportManagerApplicationForUser', $mockTma);

        $mockResponse = m::mock();

        // Expectations
        $this->sut->shouldReceive('params')
            ->with('child_id')
            ->andReturn(111)
            ->shouldReceive('getCurrentUser')
            ->andReturn($user)
            ->shouldReceive('getIdentifier')
            ->andReturn(222);

        $mockTma->shouldReceive('process')
            ->with(['userId' => 111, 'applicationId' => 222])
            ->andReturn($mockResponse);

        $mockResponse->shouldReceive('getData')
            ->andReturn(['linkId' => 444]);

        $this->sut->shouldReceive('redirect->toRouteAjax')
            ->with(null, ['action' => 'details', 'child_id' => 444], [], true)
            ->andReturn('RESPONSE');

        $this->assertEquals('RESPONSE', $this->sut->addTmAction());
    }

    public function testAddTmActionWithDifferentUserGet()
    {
        $this->markTestIncomplete();

        $user = [
            'id' => 333
        ];

        $selectedUser = [
            'contactDetails' => [
                'emailAddress' => 'foo@bar.com',
                'person' => [
                    'forename' => 'foo',
                    'familyName' => 'bar',
                    'birthDate' => '1975-05-30'
                ]
            ]
        ];

        $expectedFormData = [
            'data' => [
                'forename' => 'foo',
                'familyName' => 'bar',
                'email' => 'foo@bar.com',
                'birthDate' => '1975-05-30'
            ]
        ];

        // Mocks
        $mockForm = $this->createMockForm('Lva\AddTransportManagerDetails');
        $mockUser = m::mock();
        $this->sm->setService('Entity\User', $mockUser);

        // Expectations
        $this->sut->shouldReceive('params')
            ->with('child_id')
            ->andReturn(111)
            ->shouldReceive('getCurrentUser')
            ->andReturn($user)
            ->shouldReceive('render')
            ->once()
            ->with('addTm-transport_managers', $mockForm)
            ->andReturn('RESPONSE');

        $mockUser->shouldReceive('getUserDetails')
            ->with(111)
            ->andReturn($selectedUser);

        $mockForm->shouldReceive('get->get->setTokens')
            ->once()
            ->with(['foo@bar.com']);

        $mockForm->shouldReceive('setData')
            ->once()
            ->with($expectedFormData);

        $this->assertEquals('RESPONSE', $this->sut->addTmAction());
    }

    public function testAddTmActionWithDifferentUserPostInvalid()
    {
        $this->markTestIncomplete();

        $user = [
            'id' => 333
        ];

        $selectedUser = [
            'contactDetails' => [
                'emailAddress' => 'foo@bar.com',
                'person' => [
                    'forename' => 'foo',
                    'familyName' => 'bar',
                    'birthDate' => '1975-05-30'
                ]
            ]
        ];

        $expectedFormData = [
            'data' => [
                'forename' => 'foo',
                'familyName' => 'bar',
                'email' => 'foo@bar.com',
                'birthDate' => '1975-05-29'
            ]
        ];

        $postData = ['data' => ['birthDate' => '1975-05-29']];

        $this->setPost($postData);

        // Mocks
        $mockForm = $this->createMockForm('Lva\AddTransportManagerDetails');
        $mockUser = m::mock();
        $this->sm->setService('Entity\User', $mockUser);

        // Expectations
        $this->sut->shouldReceive('params')
            ->with('child_id')
            ->andReturn(111)
            ->shouldReceive('getCurrentUser')
            ->andReturn($user)
            ->shouldReceive('render')
            ->once()
            ->with('addTm-transport_managers', $mockForm)
            ->andReturn('RESPONSE');

        $mockUser->shouldReceive('getUserDetails')
            ->with(111)
            ->andReturn($selectedUser);

        $mockForm->shouldReceive('get->get->setTokens')
            ->once()
            ->with(['foo@bar.com']);

        $mockForm->shouldReceive('setData')
            ->once()
            ->with($expectedFormData)
            ->shouldReceive('isValid')
            ->once()
            ->andReturn(false);

        $this->assertEquals('RESPONSE', $this->sut->addTmAction());
    }

    public function testAddTmActionWithDifferentUserPostValid()
    {
        $this->markTestIncomplete();

        $user = [
            'id' => 333
        ];

        $selectedUser = [
            'contactDetails' => [
                'emailAddress' => 'foo@bar.com',
                'person' => [
                    'forename' => 'foo',
                    'familyName' => 'bar',
                    'birthDate' => '1975-05-30'
                ]
            ]
        ];

        $expectedFormData = [
            'data' => [
                'forename' => 'foo',
                'familyName' => 'bar',
                'email' => 'foo@bar.com',
                'birthDate' => '1975-05-29'
            ]
        ];

        $expectedParams = [
            'userId' => 111,
            'applicationId' => 222,
            'dob' => '1975-05-29'
        ];

        $postData = ['data' => ['birthDate' => '1975-05-29']];

        $this->setPost($postData);

        // Mocks
        $mockForm = $this->createMockForm('Lva\AddTransportManagerDetails');
        $mockUser = m::mock();
        $bsm = m::mock('\Common\BusinessService\BusinessServiceManager')->makePartial();
        $mockSendTma = m::mock('\Common\BusinessService\BusinessServiceInterface');
        $mockFlashMessenger = m::mock();

        $this->sm->setService('BusinessServiceManager', $bsm);
        $this->sm->setService('Entity\User', $mockUser);
        $bsm->setService('Lva\SendTransportManagerApplication', $mockSendTma);
        $this->sm->setService('Helper\FlashMessenger', $mockFlashMessenger);

        // Expectations
        $this->sut->shouldReceive('params')
            ->with('child_id')
            ->andReturn(111)
            ->shouldReceive('getCurrentUser')
            ->andReturn($user)
            ->shouldReceive('getIdentifier')
            ->andReturn(222);

        $this->sut->shouldReceive('redirect->toRouteAjax')
            ->with(null, ['action' => null], [], true)
            ->andReturn('RESPONSE');

        $mockUser->shouldReceive('getUserDetails')
            ->with(111)
            ->andReturn($selectedUser);

        $mockForm->shouldReceive('get->get->setTokens')
            ->once()
            ->with(['foo@bar.com']);

        $mockForm->shouldReceive('setData')
            ->once()
            ->with($expectedFormData)
            ->shouldReceive('isValid')
            ->once()
            ->andReturn(true)
            ->shouldReceive('getData')
            ->once()
            ->andReturn($expectedFormData);

        $mockSendTma->shouldReceive('process')
            ->once()
            ->with($expectedParams);

        $mockFlashMessenger->shouldReceive('addSuccessMessage')
            ->once()
            ->with('lva-tm-sent-success');

        $this->assertEquals('RESPONSE', $this->sut->addTmAction());
    }

    public function testRestoreAction()
    {
        $this->markTestIncomplete();

        $mockTmlEntityService = m::mock();
        $this->sm->setService('Entity\TransportManagerLicence', $mockTmlEntityService);

        $mockTmaEntityService = m::mock();
        $this->sm->setService('Entity\TransportManagerApplication', $mockTmaEntityService);

        $this->sut->shouldReceive('params')->once()->with('child_id')->andReturn('4,L7');
        $this->sut->shouldReceive('getIdentifier')->once()->andReturn(121);

        $mockTmlEntityService->shouldReceive('getTransportManagerLicence')
            ->once()
            ->with(7)
            ->andReturn(['transportManager' => ['id' => 55]]);

        $mockTmaEntityService->shouldReceive('getByApplicationTransportManager')
            ->once()
            ->with(121, 55)
            ->andReturn(['Results' => [['id' => 185], ['id' => 444]]]);

        $mockBusServ = m::mock();
        $bsm = m::mock('\Common\BusinessService\BusinessServiceManager')->makePartial();
        $this->sm->setService('BusinessServiceManager', $bsm);
        $bsm->shouldReceive('get')->once()->with('Lva\DeleteTransportManagerApplication')->andReturn($mockBusServ);

        $mockBusServ->shouldReceive('process')->once()->with(['ids' => [4, 185, 444]]);

        $this->sut->shouldReceive('redirect->toRouteAjax')->once()->andReturn('RESPONSE');

        $this->assertEquals('RESPONSE', $this->sut->restoreAction());
    }
}
