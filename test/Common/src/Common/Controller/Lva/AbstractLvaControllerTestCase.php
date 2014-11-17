<?php

namespace CommonTest\Controller\Lva;

use PHPUnit_Framework_TestCase;
use CommonTest\Bootstrap;
use \Mockery as m;

/**
 * Helper functions for testing LVA controllers
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
abstract class AbstractLvaControllerTestCase extends PHPUnit_Framework_TestCase
{
    protected $sm;
    protected $sut;
    protected $request;
    protected $form;
    protected $view;
    protected $formHelper;
    protected $services = [];

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();
        $this->request = m::mock('\Zend\Http\Request')->makePartial();
    }

    public function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    protected function mockController($className)
    {
        $this->sut = m::mock($className)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $this->sut->setServiceLocator($this->sm);

        $this->sut
            ->shouldReceive('getRequest')
            ->andReturn($this->request);
    }

    protected function mockRender()
    {
        $this->sut->shouldReceive('render')
            ->once()
            ->andReturnUsing(
                function ($view, $form = null) {

                    /**
                     * assign the view variable so we can interrogate it later
                     */
                    $this->view = $view;

                    /*
                     * but also return it, since that's a closer simulation
                     * of what 'render' would normally do
                     */

                    return $this->view;
                }
            );

        return $this->sut;
    }

    protected function setPost($data = [])
    {
        $this->request
            ->shouldReceive('isPost')
            ->andReturn(true)
            ->shouldReceive('getPost')
            ->andReturn($data);
    }

    protected function setService($key, $value)
    {
        $this->sm->setService($key, $value);
    }

    protected function shouldRemoveElements($form, $elements)
    {
        $helper = $this->getMockFormHelper();
        foreach ($elements as $e) {
            $helper->shouldReceive('remove')
                ->with($form, $e)
                ->andReturn($helper);
        }
    }

    protected function createMockForm($formName)
    {
        $mockForm = m::mock('\Common\Form\Form');

        $formHelper = $this->getMockFormHelper();

        $formHelper
            ->shouldReceive('createForm')
            ->with($formName)
            ->andReturn($mockForm);

        return $mockForm;
    }

    protected function getMockFormHelper()
    {
        if ($this->formHelper === null) {
            $this->formHelper = m::mock('\Common\Service\Helper\FormHelperService');
            $this->setService('Helper\Form', $this->formHelper);
        }
        return $this->formHelper;
    }

    protected function mockOrganisationId($id)
    {
        $this->sut
            ->shouldReceive('getCurrentOrganisationId')
            ->andReturn($id);
    }

    protected function mockEntity($service, $method)
    {
        return $this->mockService('Entity\\' . $service, $method);
    }

    protected function mockService($service, $method)
    {
        if (!isset($this->services[$service])) {
            $mock = m::mock();
            $this->services[$service] = $mock;
            $this->setService($service, $mock);
        }

        $mock = $this->services[$service];
        $expectation = $mock->shouldReceive($method);

        return $expectation;
    }

    protected function shouldRemoveAddAnother($form, $fieldset = 'form-actions')
    {
        $form->shouldReceive('get')
            ->with($fieldset)
            ->andReturn(
                m::mock()
                ->shouldReceive('remove')
                ->with('addAnother')
                ->getMock()
            )
            ->getMock();
    }
}
