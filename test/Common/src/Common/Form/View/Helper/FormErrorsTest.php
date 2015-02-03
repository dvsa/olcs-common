<?php

/**
 * Form Errors Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Form\View\Helper;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Form\View\Helper\FormErrors;

/**
 * Form Errors Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class FormErrorsTest extends MockeryTestCase
{
    protected $sut;

    protected $view;

    public function setUp()
    {
        $this->view = m::mock('\Zend\View\Renderer\RendererInterface');

        $this->sut = new FormErrors();
        $this->sut->setView($this->view);
    }

    public function testInvokeWithoutForm()
    {
        $form = null;

        $sut = $this->sut;

        $this->assertSame($this->sut, $sut($form));
    }

    public function testInvokeWithoutMessageWithoutValidated()
    {
        $form = m::mock('\Zend\Form\Form');

        $sut = $this->sut;

        // Expectations
        $form->shouldReceive('hasValidated')
            ->andReturn(false);

        $this->assertEquals('', $sut($form));
    }

    public function testInvokeWithoutMessageWithValid()
    {
        $form = m::mock('\Zend\Form\Form');

        $sut = $this->sut;

        // Expectations
        $form->shouldReceive('hasValidated')
            ->andReturn(true)
            ->shouldReceive('isValid')
            ->andReturn(true);

        $this->assertEquals('', $sut($form));
    }

    public function testInvokeRenderWithoutMessages()
    {
        $form = m::mock('\Zend\Form\Form');
        $messages = [];
        $expected = '';

        $sut = $this->sut;

        // Expectations
        $this->view->shouldReceive('translate')
            ->andReturnUsing(array($this, 'mockTranslate'));

        $form->shouldReceive('hasValidated')
            ->andReturn(true)
            ->shouldReceive('isValid')
            ->andReturn(false)
            ->shouldReceive('getMessages')
            ->andReturn($messages);

        $this->assertEquals($expected, $sut($form));
    }

    public function testInvokeRenderWithMessagesWithoutLabelOrAnchor()
    {
        $messages = [
            'foo' => [
                'bar',
                'cake'
            ]
        ];
        $expected = '/(\s+)?<div class="validation-summary">(\s+)?'
            . '<h3>form-errors-translated<\/h3>(\s+)?'
            . '<ol class="validation-summary__list">(\s+)?'
            . '<li class="validation-summary__item">(\s+)?Bar-translated(\s+)?<\/li>(\s+)?'
            . '<li class="validation-summary__item">(\s+)?Cake-translated(\s+)?<\/li>(\s+)?'
            . '<\/ol>(\s+)?'
            . '<\/div>/';

        $sut = $this->sut;

        // Mocks
        $form = m::mock('\Zend\Form\Form');
        $mockFoo = m::mock('\Zend\Form\Element');

        // Expectations
        $this->view->shouldReceive('translate')
            ->andReturnUsing(array($this, 'mockTranslate'));

        $form->shouldReceive('hasValidated')
            ->andReturn(true)
            ->shouldReceive('isValid')
            ->andReturn(false)
            ->shouldReceive('getMessages')
            ->andReturn($messages);

        $form->shouldReceive('get')
            ->with('foo')
            ->andReturn($mockFoo);

        $mockFoo
            ->shouldReceive('getOption')
            ->with('short-label')
            ->andReturn(null)
            ->shouldReceive('getOption')
            ->with('fieldset-attributes')
            ->andReturn(null)
            ->shouldReceive('getOption')
            ->with('label_attributes')
            ->andReturn(null)
            ->shouldReceive('getAttribute')
            ->with('id')
            ->andReturn(null);

        $this->assertRegExp($expected, $sut($form));
    }

    public function testInvokeRenderWithMessagesWithAnchor()
    {
        $messages = [
            'foo' => [
                'bar',
                'cake'
            ]
        ];
        $expected = '/(\s+)?<div class="validation-summary">(\s+)?'
            . '<h3>form-errors-translated<\/h3>(\s+)?'
            . '<ol class="validation-summary__list">(\s+)?'
            . '<li class="validation-summary__item">(\s+)?<a href="#foo-id">Bar-translated<\/a>(\s+)?<\/li>(\s+)?'
            . '<li class="validation-summary__item">(\s+)?<a href="#foo-id">Cake-translated<\/a>(\s+)?<\/li>(\s+)?'
            . '<\/ol>(\s+)?'
            . '<\/div>/';

        $sut = $this->sut;

        // Mocks
        $form = m::mock('\Zend\Form\Form');
        $mockFoo = m::mock('\Zend\Form\Element');

        // Expectations
        $this->view->shouldReceive('translate')
            ->andReturnUsing(array($this, 'mockTranslate'));

        $form->shouldReceive('hasValidated')
            ->andReturn(true)
            ->shouldReceive('isValid')
            ->andReturn(false)
            ->shouldReceive('getMessages')
            ->andReturn($messages);

        $form->shouldReceive('get')
            ->with('foo')
            ->andReturn($mockFoo);

        $mockFoo
            ->shouldReceive('getOption')
            ->with('short-label')
            ->andReturn(null)
            ->shouldReceive('getOption')
            ->with('fieldset-attributes')
            ->andReturn(null)
            ->shouldReceive('getOption')
            ->with('label_attributes')
            ->andReturn(null)
            ->shouldReceive('getAttribute')
            ->with('id')
            ->andReturn('foo-id');

        $this->assertRegExp($expected, $sut($form));
    }

    public function testInvokeRenderWithMessagesWithAnchor2()
    {
        $messages = [
            'foo' => [
                'bar',
                'cake'
            ]
        ];
        $expected = '/(\s+)?<div class="validation-summary">(\s+)?'
            . '<h3>form-errors-translated<\/h3>(\s+)?'
            . '<ol class="validation-summary__list">(\s+)?'
            . '<li class="validation-summary__item">(\s+)?<a href="#foo-id">Bar-translated<\/a>(\s+)?<\/li>(\s+)?'
            . '<li class="validation-summary__item">(\s+)?<a href="#foo-id">Cake-translated<\/a>(\s+)?<\/li>(\s+)?'
            . '<\/ol>(\s+)?'
            . '<\/div>/';

        $sut = $this->sut;

        // Mocks
        $form = m::mock('\Zend\Form\Form');
        $mockFoo = m::mock('\Zend\Form\Element');

        // Expectations
        $this->view->shouldReceive('translate')
            ->andReturnUsing(array($this, 'mockTranslate'));

        $form->shouldReceive('hasValidated')
            ->andReturn(true)
            ->shouldReceive('isValid')
            ->andReturn(false)
            ->shouldReceive('getMessages')
            ->andReturn($messages);

        $form->shouldReceive('get')
            ->with('foo')
            ->andReturn($mockFoo);

        $mockFoo
            ->shouldReceive('getOption')
            ->with('short-label')
            ->andReturn(null)
            ->shouldReceive('getOption')
            ->with('fieldset-attributes')
            ->andReturn(null)
            ->shouldReceive('getOption')
            ->with('label_attributes')
            ->andReturn(['id' => 'foo-id']);

        $this->assertRegExp($expected, $sut($form));
    }

    public function testInvokeRenderWithMessagesWithAnchor3()
    {
        $messages = [
            'foo' => [
                'bar',
                'cake'
            ]
        ];
        $expected = '/(\s+)?<div class="validation-summary">(\s+)?'
            . '<h3>form-errors-translated<\/h3>(\s+)?'
            . '<ol class="validation-summary__list">(\s+)?'
            . '<li class="validation-summary__item">(\s+)?<a href="#foo-id">Bar-translated<\/a>(\s+)?<\/li>(\s+)?'
            . '<li class="validation-summary__item">(\s+)?<a href="#foo-id">Cake-translated<\/a>(\s+)?<\/li>(\s+)?'
            . '<\/ol>(\s+)?'
            . '<\/div>/';

        $sut = $this->sut;

        // Mocks
        $form = m::mock('\Zend\Form\Form');
        $mockFoo = m::mock('\Zend\Form\Element');

        // Expectations
        $this->view->shouldReceive('translate')
            ->andReturnUsing(array($this, 'mockTranslate'));

        $form->shouldReceive('hasValidated')
            ->andReturn(true)
            ->shouldReceive('isValid')
            ->andReturn(false)
            ->shouldReceive('getMessages')
            ->andReturn($messages);

        $form->shouldReceive('get')
            ->with('foo')
            ->andReturn($mockFoo);

        $mockFoo
            ->shouldReceive('getOption')
            ->with('short-label')
            ->andReturn(null)
            ->shouldReceive('getOption')
            ->with('fieldset-attributes')
            ->andReturn(['id' => 'foo-id']);

        $this->assertRegExp($expected, $sut($form));
    }

    public function testInvokeRenderWithShortLabelAndAnchor()
    {
        $messages = [
            'foo' => [
                'bar',
                'cake'
            ]
        ];
        $expected = '/(\s+)?<div class="validation-summary">(\s+)?'
            . '<h3>form-errors-translated<\/h3>(\s+)?'
            . '<ol class="validation-summary__list">(\s+)?'
            . '<li class="validation-summary__item">(\s+)?'
            . '<a href="#foo-id">\'foo-label-translated\' bar-translated-translated<\/a>(\s+)?'
            . '<\/li>(\s+)?'
            . '<li class="validation-summary__item">(\s+)?'
            . '<a href="#foo-id">\'foo-label-translated\' cake-translated-translated<\/a>(\s+)?'
            . '<\/li>(\s+)?'
            . '<\/ol>(\s+)?'
            . '<\/div>/';

        $sut = $this->sut;

        // Mocks
        $form = m::mock('\Zend\Form\Form');
        $mockFoo = m::mock('\Zend\Form\Element');

        // Expectations
        $this->view->shouldReceive('translate')
            ->andReturnUsing(array($this, 'mockTranslate'));

        $form->shouldReceive('hasValidated')
            ->andReturn(true)
            ->shouldReceive('isValid')
            ->andReturn(false)
            ->shouldReceive('getMessages')
            ->andReturn($messages);

        $form->shouldReceive('get')
            ->with('foo')
            ->andReturn($mockFoo);

        $mockFoo
            ->shouldReceive('getOption')
            ->with('short-label')
            ->andReturn('foo-label')
            ->shouldReceive('getOption')
            ->with('fieldset-attributes')
            ->andReturn(['id' => 'foo-id']);

        $this->assertRegExp($expected, $sut($form));
    }

    public function testInvokeRenderWithShortLabelWithoutAnchor()
    {
        $messages = [
            'foo' => [
                'bar',
                'cake'
            ]
        ];
        $expected = '/(\s+)?<div class="validation-summary">(\s+)?'
            . '<h3>form-errors-translated<\/h3>(\s+)?'
            . '<ol class="validation-summary__list">(\s+)?'
            . '<li class="validation-summary__item">(\s+)?\'foo-label-translated\' bar-translated-translated(\s+)?'
            . '<\/li>(\s+)?'
            . '<li class="validation-summary__item">(\s+)?\'foo-label-translated\' cake-translated-translated(\s+)?'
            . '<\/li>(\s+)?'
            . '<\/ol>(\s+)?'
            . '<\/div>/';

        $sut = $this->sut;

        // Mocks
        $form = m::mock('\Zend\Form\Form');
        $mockFoo = m::mock('\Zend\Form\Element');

        // Expectations
        $this->view->shouldReceive('translate')
            ->andReturnUsing(array($this, 'mockTranslate'));

        $form->shouldReceive('hasValidated')
            ->andReturn(true)
            ->shouldReceive('isValid')
            ->andReturn(false)
            ->shouldReceive('getMessages')
            ->andReturn($messages);

        $form->shouldReceive('get')
            ->with('foo')
            ->andReturn($mockFoo);

        $mockFoo
            ->shouldReceive('getOption')
            ->with('short-label')
            ->andReturn('foo-label')
            ->shouldReceive('getOption')
            ->with('fieldset-attributes')
            ->andReturn(null)
            ->shouldReceive('getOption')
            ->with('label_attributes')
            ->andReturn(null)
            ->shouldReceive('getAttribute')
            ->with('id')
            ->andReturn(null);

        $this->assertRegExp($expected, $sut($form));
    }

    public function mockTranslate($text)
    {
        return $text . '-translated';
    }
}
