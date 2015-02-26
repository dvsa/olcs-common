<?php

/**
 * Crud Controller Trait Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Controller\Crud;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Crud Controller Trait Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CrudControllerTraitTest extends MockeryTestCase
{
    protected $sut;

    public function setUp()
    {
        $this->sut = m::mock('\CommonTest\Controller\Crud\Stubs\CrudControllerTraitStub')
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();
    }

    public function testConfirmDelete()
    {
        // Params
        $crudService = m::mock();
        $pageTitle = 'title';
        $sectionText = 'section-text';
        $id = 123;

        // Mocks
        $mockForm = m::mock('\Zend\Form\Form');
        $mockRequest = m::mock();

        // Expectations
        $this->sut->shouldReceive('getRequest')
            ->andReturn($mockRequest)
            ->shouldReceive('renderForm')
            ->with($mockForm, 'title', null, ['sectionText' => 'section-text'])
            ->andReturn('RENDER');

        $crudService->shouldReceive('getDeleteForm')
            ->with($mockRequest)
            ->andReturn($mockForm);

        $mockRequest->shouldReceive('isPost')
            ->andReturn(false);

        $this->assertEquals('RENDER', $this->sut->callConfirmDelete($crudService, $pageTitle, $sectionText, $id));
    }

    public function testConfirmDeleteWithPostWithoutRedirect()
    {
        // Params
        $crudService = m::mock();
        $pageTitle = 'title';
        $sectionText = 'section-text';
        $id = 123;

        // Mocks
        $mockRequest = m::mock();

        // Expectations
        $this->sut->shouldReceive('getRequest')
            ->andReturn($mockRequest)
            ->shouldReceive('notFoundAction')
            ->andReturn(404);

        $crudService->shouldReceive('processDelete')
            ->with([123])
            ->andReturn(null);

        $mockRequest->shouldReceive('isPost')
            ->andReturn(true);

        $this->assertEquals(404, $this->sut->callConfirmDelete($crudService, $pageTitle, $sectionText, $id));
    }

    public function testConfirmDeleteWithPost()
    {
        // Params
        $crudService = m::mock();
        $pageTitle = 'title';
        $sectionText = 'section-text';
        $id = 123;

        // Mocks
        $mockRedirectPlugin = m::mock();
        $mockRedirect = m::mock('\Common\Util\Redirect');
        $mockRequest = m::mock();

        // Expectations
        $this->sut->shouldReceive('getRequest')
            ->andReturn($mockRequest)
            ->shouldReceive('redirect')
            ->andReturn($mockRedirectPlugin);

        $crudService->shouldReceive('processDelete')
            ->with([123])
            ->andReturn($mockRedirect);

        $mockRequest->shouldReceive('isPost')
            ->andReturn(true);

        $mockRedirect->shouldReceive('process')
            ->with($mockRedirectPlugin)
            ->andReturn('REDIRECT');

        $this->assertEquals('REDIRECT', $this->sut->callConfirmDelete($crudService, $pageTitle, $sectionText, $id));
    }

    public function testAddOrEditFormWithNullResponse()
    {
        // Params
        $crudService = m::mock();
        $pageTitle = 'title';
        $id = 123;

        // Mocks
        $mockRequest = m::mock();

        // Expectations
        $this->sut->shouldReceive('getRequest')
            ->andReturn($mockRequest)
            ->shouldReceive('notFoundAction')
            ->andReturn(404);

        $crudService->shouldReceive('processForm')
            ->with($mockRequest, $id)
            ->andReturn(null);

        $this->assertEquals(404, $this->sut->callAddOrEditForm($crudService, $pageTitle, $id));
    }

    public function testAddOrEditFormWithFormResponse()
    {
        // Params
        $crudService = m::mock();
        $pageTitle = 'title';
        $id = 123;

        // Mocks
        $mockForm = m::mock('\Zend\Form\Form');
        $mockRequest = m::mock();

        // Expectations
        $this->sut->shouldReceive('getRequest')
            ->andReturn($mockRequest)
            ->shouldReceive('renderForm')
            ->with($mockForm, 'title')
            ->andReturn('RENDER');

        $crudService->shouldReceive('processForm')
            ->with($mockRequest, $id)
            ->andReturn($mockForm);

        $this->assertEquals('RENDER', $this->sut->callAddOrEditForm($crudService, $pageTitle, $id));
    }

    public function testAddOrEditFormWithRedirectResponse()
    {
        // Params
        $crudService = m::mock();
        $pageTitle = 'title';
        $id = 123;

        // Mocks
        $mockRedirectPlugin = m::mock();
        $mockRedirect = m::mock('\Common\Util\Redirect');
        $mockRequest = m::mock();

        // Expectations
        $this->sut->shouldReceive('getRequest')
            ->andReturn($mockRequest)
            ->shouldReceive('redirect')
            ->andReturn($mockRedirectPlugin);

        $crudService->shouldReceive('processForm')
            ->with($mockRequest, $id)
            ->andReturn($mockRedirect);

        $mockRedirect->shouldReceive('process')
            ->with($mockRedirectPlugin)
            ->andReturn('REDIRECT');

        $this->assertEquals('REDIRECT', $this->sut->callAddOrEditForm($crudService, $pageTitle, $id));
    }

    public function testRenderTable()
    {
        // Params
        $table = m::mock('\Common\Service\Table\TableBuilder');
        $title = 'foo';
        $subTitle = 'bar';

        // Mocks
        $mockRequest = m::mock();

        // Expecations
        $this->sut->shouldReceive('getRequest')
            ->andReturn($mockRequest);

        $mockRequest->shouldReceive('isXmlHttpRequest')
            ->andReturn(false);

        $view = $this->sut->callRenderTable($table, $title, $subTitle);

        $this->assertEquals('layout/base', $view->getTemplate());
    }
}
