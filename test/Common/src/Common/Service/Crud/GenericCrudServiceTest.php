<?php

/**
 * Generic Crud Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Crud;

use CommonTest\Bootstrap;
use Common\Service\Crud\GenericCrudService;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Generic Crud Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class GenericCrudServiceTest extends MockeryTestCase
{
    protected $sm;

    protected $sut;

    public function setUp()
    {
        $this->sut =  new GenericCrudService();

        $this->sm = Bootstrap::getServiceManager();
        $this->sut->setServiceLocator($this->sm);
    }

    public function testProcessFormWithPostWithAddWithValidForm()
    {
        // Params
        $service = m::mock('\Common\Service\Crud\GenericProcessFormInterface')->makePartial();
        $request = m::mock('\Zend\Http\Request');
        $id = null;
        $postData = ['foo' => 'unfiltered'];
        $formData = ['foo' => 'filtered'];

        // Mocks
        $mockForm = m::mock('\Zend\Form\Form');
        $mockFormHelper = m::mock();
        $this->sm->setService('Helper\Form', $mockFormHelper);

        // Expectations
        $request->shouldReceive('isPost')
            ->andReturn(true)
            ->shouldReceive('getPost')
            ->andReturn($postData);

        $service->shouldReceive('getForm')
            ->andReturn($mockForm)
            ->shouldReceive('isFormValid')
            ->with($mockForm, $id)
            ->andReturn(true)
            ->shouldReceive('processSave')
            ->with($formData, $id)
            ->andReturn('RESPONSE');

        $mockFormHelper->shouldReceive('setFormActionFromRequest')
            ->with($mockForm, $request);

        $mockForm->shouldReceive('setData')
            ->with($postData)
            ->andReturnSelf()
            ->shouldReceive('getData')
            ->andReturn($formData);

        // Assertions
        $response = $this->sut->processForm($service, $request, $id);

        $this->assertEquals('RESPONSE', $response);
    }

    public function testProcessFormWithPostWithAddWithInvalidForm()
    {
        // Params
        $service = m::mock('\Common\Service\Crud\GenericProcessFormInterface')->makePartial();
        $request = m::mock('\Zend\Http\Request');
        $id = null;
        $postData = ['foo' => 'unfiltered'];
        $formData = ['foo' => 'filtered'];

        // Mocks
        $mockForm = m::mock('\Zend\Form\Form');
        $mockFormHelper = m::mock();
        $this->sm->setService('Helper\Form', $mockFormHelper);

        // Expectations
        $request->shouldReceive('isPost')
            ->andReturn(true)
            ->shouldReceive('getPost')
            ->andReturn($postData);

        $service->shouldReceive('getForm')
            ->andReturn($mockForm)
            ->shouldReceive('isFormValid')
            ->with($mockForm, $id)
            ->andReturn(false);

        $mockFormHelper->shouldReceive('setFormActionFromRequest')
            ->with($mockForm, $request);

        $mockForm->shouldReceive('setData')
            ->with($postData)
            ->andReturnSelf()
            ->shouldReceive('getData')
            ->andReturn($formData);

        // Assertions
        $response = $this->sut->processForm($service, $request, $id);

        $this->assertEquals($mockForm, $response);
    }

    public function testProcessFormWithGetWithAdd()
    {
        // Params
        $service = m::mock('\Common\Service\Crud\GenericProcessFormInterface')->makePartial();
        $request = m::mock('\Zend\Http\Request');
        $id = null;
        $defaultData = ['foo' => 'bar'];

        // Mocks
        $mockForm = m::mock('\Zend\Form\Form');
        $mockFormHelper = m::mock();
        $this->sm->setService('Helper\Form', $mockFormHelper);

        // Expectations
        $request->shouldReceive('isPost')
            ->andReturn(false);

        $service->shouldReceive('getForm')
            ->andReturn($mockForm)
            ->shouldReceive('getDefaultFormData')
            ->andReturn($defaultData);

        $mockFormHelper->shouldReceive('setFormActionFromRequest')
            ->with($mockForm, $request);

        $mockForm->shouldReceive('setData')
            ->with($defaultData)
            ->andReturnSelf();

        // Assertions
        $response = $this->sut->processForm($service, $request, $id);

        $this->assertEquals($mockForm, $response);
    }

    public function testProcessFormWithGetWithEditWithMissingRecord()
    {
        // Params
        $service = m::mock('\Common\Service\Crud\GenericProcessFormInterface')->makePartial();
        $request = m::mock('\Zend\Http\Request');
        $id = 123;

        // Mocks
        $mockFlashMessenger = m::mock();
        $mockRedirectPlugin = m::mock();
        $this->sm->setService('Helper\FlashMessenger', $mockFlashMessenger);

        // Expectations
        $request->shouldReceive('isPost')
            ->andReturn(false);

        $service->shouldReceive('getRecordData')
            ->with($id)
            ->andReturn(null);

        $mockFlashMessenger->shouldReceive('addErrorMessage')
            ->with('record-not-found');

        // Assertions
        $response = $this->sut->processForm($service, $request, $id);

        $this->assertInstanceOf('Common\Util\Redirect', $response);

        // Assert that we are redirected back to the list
        $mockRedirectPlugin->shouldReceive('toRoute')->with(null, [], [], false);
        $response->process($mockRedirectPlugin);
    }

    public function testProcessFormWithGetWithEdit()
    {
        // Params
        $service = m::mock('\Common\Service\Crud\GenericProcessFormInterface')->makePartial();
        $request = m::mock('\Zend\Http\Request');
        $id = 123;
        $record = ['foo' => 'bar'];

        // Mocks
        $mockForm = m::mock('\Zend\Form\Form');
        $mockFormHelper = m::mock();
        $this->sm->setService('Helper\Form', $mockFormHelper);

        // Expectations
        $request->shouldReceive('isPost')
            ->andReturn(false);

        $service->shouldReceive('getForm')
            ->andReturn($mockForm)
            ->shouldReceive('getRecordData')
            ->with($id)
            ->andReturn($record);

        $mockFormHelper->shouldReceive('setFormActionFromRequest')
            ->with($mockForm, $request);

        $mockForm->shouldReceive('setData')
            ->with($record)
            ->andReturnSelf();

        // Assertions
        $response = $this->sut->processForm($service, $request, $id);

        $this->assertSame($mockForm, $response);
    }

    public function testProcessFormWithPostWithEditWithVersionConflict()
    {
        // Params
        $service = m::mock('\Common\Service\Crud\GenericProcessFormInterface')->makePartial();
        $request = m::mock('\Zend\Http\Request');
        $id = 123;
        $record = ['foo' => 'bar'];
        $postData = ['foo' => 'blah'];
        $formData = ['blah' => 'blah'];

        // Mocks
        $mockForm = m::mock('\Zend\Form\Form');
        $mockFormHelper = m::mock();
        $this->sm->setService('Helper\Form', $mockFormHelper);
        $mockFlashMessenger = m::mock();
        $this->sm->setService('Helper\FlashMessenger', $mockFlashMessenger);

        // Expectations
        $request->shouldReceive('isPost')
            ->andReturn(true)
            ->shouldReceive('getPost')
            ->andReturn($postData);

        $service->shouldReceive('getForm')
            ->andReturn($mockForm)
            ->shouldReceive('isFormValid')
            ->with($mockForm, $id)
            ->andReturn(true)
            ->shouldReceive('processSave')
            ->with($formData, $id)
            ->andThrow('\Common\Exception\ResourceConflictException')
            ->shouldReceive('getRecordData')
            ->with($id)
            ->andReturn($record);

        $mockFormHelper->shouldReceive('setFormActionFromRequest')
            ->with($mockForm, $request);

        $mockForm->shouldReceive('setData')
            ->with($postData)
            ->andReturnSelf()
            ->shouldReceive('getData')
            ->andReturn($formData)
            ->shouldReceive('setData')
            ->with($record)
            ->andReturnSelf();

        $mockFlashMessenger->shouldReceive('addErrorMessage')
            ->with('version-conflict-message');

        // Assertions
        $response = $this->sut->processForm($service, $request, $id);

        $this->assertSame($mockForm, $response);
    }
}
