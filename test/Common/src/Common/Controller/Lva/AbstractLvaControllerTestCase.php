<?php

namespace CommonTest\Controller\Lva;

use PHPUnit_Framework_TestCase;
use CommonTest\Bootstrap;
use \Mockery as m;

abstract class AbstractLvaControllerTestCase extends PHPUnit_Framework_TestCase
{
    protected $sm;
    protected $sut;
    protected $request;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();
        $this->request = m::mock('\Zend\Http\Request')->makePartial();
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
                    $this->view = $view;
                    $this->form = $form;

                    return $this->view;
                }
            );
    }

    protected function setPost($data = [])
    {
        $this->request
            ->shouldReceive('isPost')
            ->andReturn(true)
            ->shouldReceive('getPost')
            ->andReturn($data);
    }

    protected function disableCsrf()
    {
        $oldHelper = $this->sm->get('Helper\Form');
        $formHelper = m::mock('\Common\Service\Helper\FormHelperService');
        $formHelper
            ->shouldReceive('createForm')
            ->andReturnUsing(
                function($form) use ($oldHelper) {
                    return $oldHelper->createForm($form, false);
                }
            );
        $this->sm->setService('Helper\Form', $formHelper);
    }

    protected function setService($key, $value)
    {
        $this->sm->setService($key, $value);
    }
}
