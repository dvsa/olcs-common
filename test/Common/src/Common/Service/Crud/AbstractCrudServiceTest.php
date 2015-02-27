<?php

/**
 * Abstract Crud Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Crud;

use CommonTest\Bootstrap;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Abstract Crud Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class AbstractCrudServiceTest extends MockeryTestCase
{
    protected $sut;
    protected $sm;

    public function setUp()
    {
        $this->sut = m::mock('\Common\Service\Crud\AbstractCrudService')
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $this->sm = Bootstrap::getServiceManager();
        $this->sut->setServiceLocator($this->sm);
    }

    public function testIsFormValid()
    {
        // Params
        $mockForm = m::mock('\Zend\Form\Form');

        // Expectations
        $mockForm->shouldReceive('isValid')
            ->andReturn(true);

        $this->assertTrue($this->sut->isFormValid($mockForm));
    }

    public function testGetDefaultFormData()
    {
        $this->assertEquals([], $this->sut->getDefaultFormData());
    }

    public function testGetDeleteForm()
    {
        // Params
        $mockRequest = m::mock('\Zend\Http\Request');

        // Mocks
        $mockForm = m::mock();
        $mockFormHelper = m::mock();
        $this->sm->setService('Helper\Form', $mockFormHelper);

        // Expectations
        $mockFormHelper->shouldReceive('createFormWithRequest')
            ->with('GenericDeleteConfirmation', $mockRequest)
            ->andReturn($mockForm);

        $this->assertSame($mockForm, $this->sut->getDeleteForm($mockRequest));
    }

    public function testProcessData()
    {
        // Params
        $ids = [1, 2, 3];

        // Mocks
        $mockRedirectPlugin = m::mock();
        $mockFlashMessenger = m::mock();
        $this->sm->setService('Helper\FlashMessenger', $mockFlashMessenger);

        // Expecations
        $this->sut->shouldReceive('delete')
            ->with(1)
            ->shouldReceive('delete')
            ->with(2)
            ->shouldReceive('delete')
            ->with(3);

        $mockFlashMessenger->shouldReceive('addSuccessMessage')
            ->with('record-deleted');

        $redirect = $this->sut->processDelete($ids);

        // Ensure redirect to the right place
        $mockRedirectPlugin->shouldReceive('toRouteAjax')
            ->with(null, [], [], false);

        $this->assertInstanceOf('\Common\Util\Redirect', $redirect);
        $redirect->process($mockRedirectPlugin);
    }

    public function testProcessForm()
    {
        // Params
        $mockRequest = m::mock('\Zend\Http\Request');
        $id = 134;

        // Mocks
        $mockGenericCrud = m::mock();
        $this->sm->setService('Crud\Generic', $mockGenericCrud);

        // Expecations
        $mockGenericCrud->shouldReceive('processForm')
            ->with($this->sut, $mockRequest, $id)
            ->andReturn('RESPONSE');

        $this->assertEquals('RESPONSE', $this->sut->processForm($mockRequest, $id));
    }
}
