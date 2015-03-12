<?php

/**
 * Generic Crud Controller Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Controller\Crud;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use CommonTest\Bootstrap;
use Common\Controller\Crud\GenericCrudController;

/**
 * Generic Crud Controller Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class GenericCrudControllerTest extends MockeryTestCase
{
    /**
     * @var \Common\Controller\Crud\GenericCrudController
     */
    protected $sut;

    /**
     * @var \Zend\ServiceManager\ServiceManager
     */
    protected $sm;

    /**
     * @var \Common\Service\Crud\CrudServiceInterface
     */
    protected $crudService;

    /**
     * @var \Zend\Http\Request
     */
    protected $request;

    public function setUp()
    {
        $this->request = m::mock('\Zend\Http\Request');

        // This is the only way I can find to mock the request object inside the controller
        // can't mock the SUT properly as it's declared final, and can't inject the Request in without
        // calling dispatch, or adding an extra setRequest method
        $reflectedSut = new \ReflectionClass('\Common\Controller\Crud\GenericCrudController');
        $requestProperty = $reflectedSut->getProperty('request');
        $requestProperty->setAccessible(true);

        $this->sut = new GenericCrudController();
        $requestProperty->setValue($this->sut, $this->request);

        $this->sm = Bootstrap::getServiceManager();

        $this->crudService = m::mock('\Common\Service\Crud\CrudServiceInterface');

        $this->sut->setServiceLocator($this->sm);
        $this->sut->setCrudService($this->crudService);
        $this->sut->setTranslationPrefix('crud-foo');
        $this->sut->setOption('pageLayout', 'custom-layout');

        // Tests the params - required for every method so acceptable to assert here.
        $params = ['a' => uniqid(), 'b' => uniqid()];
        $this->assertSame($params, $this->sut->setParams($params)->getParams());
    }

    public function testIndexAction()
    {
        $this->request->shouldReceive('isXmlHttpRequest')->andReturn(false);

        $this->crudService->shouldReceive('getList')->with($this->sut->getParams())->andReturn('TABLE');

        // Assertions
        $view = $this->sut->indexAction();

        $this->assertInstanceOf('\Zend\View\Model\ViewModel', $view);
        $this->assertEquals('layout/base', $view->getTemplate());
        $this->assertEquals(
            ['table' => 'TABLE', 'pageTitle' => 'crud-foo-title', 'pageSubTitle' => null],
            $view->getVariables()
        );

        $contentChildren = $view->getChildrenByCaptureTo('content');

        $this->assertCount(2, $contentChildren);
        $this->assertEquals('partials/table', $contentChildren[0]->getTemplate());
        $this->assertEquals('layout/custom-layout', $contentChildren[1]->getTemplate());
    }

    public function testAddActionWithForm()
    {
        // Mocks
        $mockForm = m::mock('\Zend\Form\Form');

        // Expectations
        $this->request->shouldReceive('isXmlHttpRequest')
            ->andReturn(false);

        $this->crudService->shouldReceive('processForm')
            ->with($this->request, null)
            ->andReturn($mockForm);

        // Assertions
        $view = $this->sut->addAction();

        $this->assertInstanceOf('\Zend\View\Model\ViewModel', $view);
        $this->assertEquals('layout/base', $view->getTemplate());
        $this->assertEquals(
            ['form' => $mockForm, 'pageTitle' => 'crud-foo-form-add', 'pageSubTitle' => null],
            $view->getVariables()
        );

        $contentChildren = $view->getChildrenByCaptureTo('content');

        $this->assertCount(2, $contentChildren);
        $this->assertEquals('partials/form', $contentChildren[0]->getTemplate());
        $this->assertEquals('layout/custom-layout', $contentChildren[1]->getTemplate());
    }

    public function testAddActionWithRedirect()
    {
        // Mocks
        $mockRedirect = m::mock('\Common\Util\Redirect');
        $mockRedirectPlugin = $this->mockRedirectPlugin();

        // Expectations
        $this->request->shouldReceive('isXmlHttpRequest')
            ->andReturn(false);

        $this->crudService->shouldReceive('processForm')
            ->with($this->request, null)
            ->andReturn($mockRedirect);

        $mockRedirect->shouldReceive('process')
            ->with($mockRedirectPlugin)
            ->andReturn('REDIRECT');

        // Assertions
        $this->assertEquals('REDIRECT', $this->sut->addAction());
    }

    public function testAddActionWithUnexpectedResponse()
    {
        // Expectations
        $this->request->shouldReceive('isXmlHttpRequest')
            ->andReturn(false);

        $this->crudService->shouldReceive('processForm')
            ->with($this->request, null)
            ->andReturn(null);

        $this->expectNotFoundAction();

        // Assertions
        $this->sut->addAction();
    }

    public function testEditActionWithForm()
    {
        // Mocks
        $mockForm = m::mock('\Zend\Form\Form');
        $this->mockParams(['id' => 123]);

        // Expectations
        $this->request->shouldReceive('isXmlHttpRequest')
            ->andReturn(false);

        $this->crudService->shouldReceive('processForm')
            ->with($this->request, 123)
            ->andReturn($mockForm);

        // Assertions
        $view = $this->sut->editAction();

        $this->assertInstanceOf('\Zend\View\Model\ViewModel', $view);
        $this->assertEquals('layout/base', $view->getTemplate());
        $this->assertEquals(
            ['form' => $mockForm, 'pageTitle' => 'crud-foo-form-edit', 'pageSubTitle' => null],
            $view->getVariables()
        );

        $contentChildren = $view->getChildrenByCaptureTo('content');

        $this->assertCount(2, $contentChildren);
        $this->assertEquals('partials/form', $contentChildren[0]->getTemplate());
        $this->assertEquals('layout/custom-layout', $contentChildren[1]->getTemplate());
    }

    public function testEditActionWithRedirect()
    {
        // Mocks
        $mockRedirect = m::mock('\Common\Util\Redirect');
        $mockRedirectPlugin = $this->mockRedirectPlugin();
        $this->mockParams(['id' => 123]);

        // Expectations
        $this->request->shouldReceive('isXmlHttpRequest')
            ->andReturn(false);

        $this->crudService->shouldReceive('processForm')
            ->with($this->request, 123)
            ->andReturn($mockRedirect);

        $mockRedirect->shouldReceive('process')
            ->with($mockRedirectPlugin)
            ->andReturn('REDIRECT');

        // Assertions
        $this->assertEquals('REDIRECT', $this->sut->editAction());
    }

    public function testEditActionWithUnexpectedResponse()
    {
        // Mocks
        $this->mockParams(['id' => 123]);

        // Expectations
        $this->request->shouldReceive('isXmlHttpRequest')
            ->andReturn(false);

        $this->crudService->shouldReceive('processForm')
            ->with($this->request, 123)
            ->andReturn(null);

        $this->expectNotFoundAction();

        // Assertions
        $this->sut->editAction();
    }

    public function testDeleteAction()
    {
        // Mocks
        $mockForm = m::mock('\Zend\Form\Form');
        $this->mockParams(['id' => 123]);

        // Expectations
        $this->request->shouldReceive('isPost')
            ->andReturn(false)
            ->shouldReceive('isXmlHttpRequest')
            ->andReturn(false);

        $this->crudService->shouldReceive('getDeleteForm')
            ->with($this->request)
            ->andReturn($mockForm);

        // Assertions
        $view = $this->sut->deleteAction();

        $this->assertInstanceOf('\Zend\View\Model\ViewModel', $view);
        $this->assertEquals('layout/base', $view->getTemplate());
        $this->assertEquals(
            [
                'form' => $mockForm,
                'pageTitle' => 'crud-foo-delete-title',
                'pageSubTitle' => null,
                'sectionText' => 'crud-foo-delete-message'
            ],
            $view->getVariables()
        );

        $contentChildren = $view->getChildrenByCaptureTo('content');

        $this->assertCount(2, $contentChildren);
        $this->assertEquals('partials/form', $contentChildren[0]->getTemplate());
        $this->assertEquals('layout/custom-layout', $contentChildren[1]->getTemplate());
    }

    public function testDeleteActionWithPost()
    {
        // Mocks
        $this->mockParams(['id' => 123]);

        // Expectations
        $this->request->shouldReceive('isPost')
            ->andReturn(true)
            ->shouldReceive('isXmlHttpRequest')
            ->andReturn(false);

        $this->crudService->shouldReceive('processDelete')
            ->with([123])
            ->andReturn(null);

        // Assertions
        $this->expectNotFoundAction();
        $this->sut->deleteAction();
    }

    public function testDeleteActionWithPostWithRedirect()
    {
        // Mocks
        $mockRedirect = m::mock('\Common\Util\Redirect');
        $mockRedirectPlugin = $this->mockRedirectPlugin();
        $this->mockParams(['id' => 123]);

        // Expectations
        $this->request->shouldReceive('isPost')
            ->andReturn(true)
            ->shouldReceive('isXmlHttpRequest')
            ->andReturn(false);

        $this->crudService->shouldReceive('processDelete')
            ->with([123])
            ->andReturn($mockRedirect);

        $mockRedirect->shouldReceive('process')
            ->with($mockRedirectPlugin)
            ->andReturn('REDIRECT');

        // Assertions
        $this->assertEquals('REDIRECT', $this->sut->deleteAction());
    }

    /**
     * @NOTE Not keen on doing this, as ZF2 could change the notFoundAction which would cause this test to break
     */
    protected function expectNotFoundAction()
    {
        $mockEvent = m::mock('\Zend\Mvc\MvcEvent');
        $this->sut->setEvent($mockEvent);

        $mockEvent->shouldReceive('getRouteMatch')
            ->andReturn(
                m::mock()
                ->shouldReceive('setParam')
                ->with('action', 'not-found')
                ->getMock()
            );
    }

    protected function mockRedirectPlugin()
    {
        $mockRedirectPlugin = m::mock('\Zend\Mvc\Controller\Plugin\PluginInterface');
        $this->sut->getPluginManager()->setAllowOverride(true)->setService('Redirect', $mockRedirectPlugin);
        $mockRedirectPlugin->shouldReceive('setController')
            ->with($this->sut);

        return $mockRedirectPlugin;
    }

    protected function mockParams($params)
    {
        $mockParams = m::mock('\Zend\Mvc\Controller\Plugin\Params');
        $this->sut->getPluginManager()->setAllowOverride(true)->setService('Params', $mockParams);
        $mockParams->shouldReceive('setController')
            ->with($this->sut);

        $mockParams->shouldReceive('__invoke')
            ->andReturnUsing(
                function ($string, $default = null) use ($params) {
                    return (isset($params[$string]) ? $params[$string] : $default);
                }
            );
    }
}
