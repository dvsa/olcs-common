<?php

namespace CommonTest\Form\View\Helper;

use Common\Form\View\Helper;
use Common\Form\View\Helper\Form as FormViewHelper;
use Mockery as m;
use Zend\Form\Element;
use Zend\Form\ElementInterface;
use Zend\Form\FieldsetInterface;
use Zend\Stdlib\PriorityQueue;
use Zend\View\HelperPluginManager;
use Zend\View\Renderer\PhpRenderer;

/**
 * @covers \Common\Form\View\Helper\Form
 */
class FormTest extends m\Adapter\Phpunit\MockeryTestCase
{
    /** @var \Zend\Form\Form */
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

        $viewHelper = new FormViewHelper();
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

        $viewHelper = new FormViewHelper();
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

        $sut($mockForm);
    }

    public function testRenderKeepEmptyFields()
    {
        //  check keepEmptyFieldset element
        $mockFieldsetKeepEmpty = m::mock(FieldsetInterface::class)
            ->shouldReceive('hasAttribute')->once()->with('keepEmptyFieldset')->andReturn(true)
            ->shouldReceive('getAttribute')->once()->with('keepEmptyFieldset')->andReturn(false)
            ->shouldReceive('count')->once()->andReturn(0)
            ->getMock();

        $mockHelper = m::mock(\Common\Form\View\Helper\FormCollection::class)
            ->shouldReceive('setReadOnly')->with(false)->once()->getMock();

        $iterator = new PriorityQueue();
        $iterator->insert($mockFieldsetKeepEmpty);

        $mockForm = m::mock(\Zend\Form\Form::class)
            ->shouldReceive('prepare')->once()->andReturnNull()
            ->shouldReceive('getIterator')->once()->andReturn($iterator)
            ->shouldReceive('getOption')->twice()->with('readonly')->andReturn(false)
            ->shouldReceive('getAttributes')->once()->andReturn([])
            ->getMock();

        /** @var \Zend\View\Renderer\RendererInterface|m\MockInterface $mockView */
        $mockView = m::mock(\Zend\View\Renderer\RendererInterface::class)
            ->shouldReceive('formCollection')->once()->andReturn($mockHelper)
            ->shouldReceive('plugin')->once()->with('formrow')->andReturn($mockHelper)
            ->shouldReceive('addTags')->never()
            ->getMock();

        $sut = new FormViewHelper();
        $sut->setView($mockView);

        $sut($mockForm);
    }

    public function testRenderFieldsetsWithHidden()
    {
        $mockElmHidden = m::mock(Element\Hidden::class);
        $mockElm = m::mock(ElementInterface::class);

        //  check Fieldset with usual element
        $mockFsWithElement = m::mock(FieldsetInterface::class)
            ->shouldReceive('hasAttribute')->once()->with('keepEmptyFieldset')->andReturn(false)
            ->shouldReceive('count')->once()->andReturn(1)
            ->shouldReceive('getElements')->once()->with()->andReturn([$mockElm])
            ->getMock();

        //  check Fieldset with Fieldse with usual element
        $mockSubFs = m::mock(FieldsetInterface::class)
            ->shouldReceive('getElements')->once()->with()->andReturn([$mockElm])
            ->getMock();

        $mockFsWithSubFs = m::mock(FieldsetInterface::class)
            ->shouldReceive('getElements')->once()->with()->andReturn([])
            ->shouldReceive('hasAttribute')->once()->with('keepEmptyFieldset')->andReturn(false)
            ->shouldReceive('count')->once()->andReturn(1)
            ->shouldReceive('getFieldsets')->once()->with()->andReturn([$mockSubFs])
            ->getMock();

        //  check Fieldset with Hidden element
        $mockFsWithHiddenElement = m::mock(FieldsetInterface::class)
            ->shouldReceive('hasAttribute')->once()->with('keepEmptyFieldset')->andReturn(false)
            ->shouldReceive('count')->once()->andReturn(1)
            ->shouldReceive('getElements')->once()->with()->andReturn([$mockElmHidden])
            ->shouldReceive('setAttribute')->once()->with('class', 'hidden')
            ->shouldReceive('getFieldsets')->once()->with()->andReturn([])
            ->getMock();

        $mockHelper = m::mock('Common\Form\View\Helper\FormCollection')
            ->shouldReceive('setReadOnly')
            ->with(false)
            ->once()
            ->getMock();

        $iterator = new PriorityQueue();
        $iterator->insert($mockFsWithElement);
        $iterator->insert($mockFsWithSubFs);
        $iterator->insert($mockFsWithHiddenElement);

        $mockForm = m::mock(\Zend\Form\Form::class)
            ->shouldReceive('prepare')->once()->andReturnNull()
            ->shouldReceive('getIterator')->once()->andReturn($iterator)
            ->shouldReceive('getOption')->twice()->with('readonly')->andReturn(false)
            ->shouldReceive('getAttributes')->once()->andReturn([])
            ->getMock();

        /** @var \Zend\View\Renderer\RendererInterface|m\MockInterface $mockView */
        $mockView = m::mock(\Zend\View\Renderer\RendererInterface::class)
            ->shouldReceive('formCollection')->times(4)->andReturn($mockHelper)
            ->shouldReceive('plugin')->once()->with('formrow')->andReturn($mockHelper)
            ->shouldReceive('addTags')->times(3)
            ->getMock();

        $sut = new FormViewHelper();
        $sut->setView($mockView);

        $sut($mockForm);
    }
}
