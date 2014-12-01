<?php

/**
 * FormTest
 *
 * @package CommonTest\Form\View\Helper
 * @author Jakub Igla <jakub.igla@gmail.com>
 */
namespace CommonTest\Form\View\Helper;

use Zend\Stdlib\PriorityQueue;
use Zend\View\HelperPluginManager;
use Zend\View\Renderer\PhpRenderer;
use Common\Form\View\Helper;
use Common\Form\View\Helper\Form as FormViewHelper;
use Mockery as m;

/**
 * FormTest
 *
 * @package CommonTest\Form\View\Helper
 * @author Jakub Igla <jakub.igla@gmail.com>
 */
class FormTest extends m\Adapter\Phpunit\MockeryTestCase
{

    protected $form;

    public function setUp()
    {
        $this->form = new \Zend\Form\Form('test');
    }

    /**
     * @outputBuffering disabled
     */
    public function testRenderFormWithElement()
    {
        $this->form->add(new \Zend\Form\Element\Text('test'));

        $helpers = new HelperPluginManager();
        $helpers->setService('formRow', new Helper\FormRow());
        $helpers->setService('formCollection', new Helper\FormCollection());
        $helpers->setService('addTags', new \Common\View\Helper\AddTags());
        $view = new PhpRenderer();
        $view->setHelperPluginManager($helpers);

        $viewHelper = new \Common\Form\View\Helper\Form();
        $viewHelper->setView($view);
        echo $viewHelper($this->form, 'form', '/');

        $this->expectOutputRegex(
            '/^<form action="(.*)" method="(POST|GET)" name="test" id="test"><div class="field "><\/div><\/form>$/'
        );
    }

    /**
     * @outputBuffering disabled
     */
    public function testRenderFormWithFieldset()
    {
        $this->form->add(new \Zend\Form\Fieldset('test'));

        $helpers = new HelperPluginManager();
        $helpers->setService('formCollection', new Helper\FormCollection());
        $helpers->setService('formRow', new Helper\FormRow());
        $helpers->setService('addTags', new \Common\View\Helper\AddTags());
        $view = new PhpRenderer();
        $view->setHelperPluginManager($helpers);

        $viewHelper = new \Common\Form\View\Helper\Form();
        $viewHelper->setView($view);
        echo $viewHelper($this->form, 'form', '/');

        $this->expectOutputRegex('/^<form action="(.*)" method="(POST|GET)" name="test" id="test"><\/form>$/');
    }

    public function testReadonly()
    {
        $mockElement = m::mock('Zend\Form\ElementInterface');
        $mockElement->shouldReceive('getName')->andReturn('name');

        $mockHelper = m::mock('Common\Form\View\Helper\FormCollection');
        $mockHelper->shouldReceive('setReadOnly')->once()->with(true);
        $mockHelper->shouldReceive('__invoke')->with($mockElement)->andReturn('element');

        $iterator = new PriorityQueue();
        $iterator->insert($mockElement);

        $mockForm = m::mock('Zend\Form\Form');
        $mockForm->shouldReceive('prepare');
        $mockForm->shouldReceive('getIterator')->andReturn($iterator);
        $mockForm->shouldReceive('getOption')->with('readonly')->andReturn(true);
        $mockForm->shouldReceive('getAttributes')->andReturn([]);

        $mockView = m::mock('Zend\View\Renderer\RendererInterface');
        $mockView->shouldReceive('formCollection')->andReturn($mockHelper);
        $mockView->shouldReceive('plugin')->with('readonlyformrow')->andReturn($mockHelper);


        $sut = new FormViewHelper();
        $sut->setView($mockView);

        $sut->__invoke($mockForm);
    }
}
