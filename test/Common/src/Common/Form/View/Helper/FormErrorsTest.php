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
        $expected = '/(\s+)?<div class="validation-summary" role="alert" id="validationSummary">(\s+)?'
            . '<h3>form-errors-translated<\/h3>(\s+)?'
            . '<p><\/p>(\s+)?'
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
            ->andReturn($messages)
            ->shouldReceive('has')
            ->once()
            ->with('foo')
            ->andReturn(true)
            ->shouldReceive('getOption')
            ->with('formErrorsTitle')
            ->andReturn(null)
            ->shouldReceive('getOption')
            ->with('formErrorsParagraph')
            ->andReturn(null);

        $form->shouldReceive('get')
            ->with('foo')
            ->andReturn($mockFoo);

        $mockFoo
            ->shouldReceive('getOption')
            ->with('short-label')
            ->andReturn(null)
            ->shouldReceive('getOption')
            ->with('error-message')
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
        $expected = '/(\s+)?<div class="validation-summary" role="alert" id="validationSummary">(\s+)?'
            . '<h3>form-errors-translated<\/h3>(\s+)?'
            . '<p><\/p>(\s+)?'
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
            ->andReturn($messages)
            ->shouldReceive('has')
            ->once()
            ->with('foo')
            ->andReturn(true)
            ->shouldReceive('getOption')
            ->with('formErrorsTitle')
            ->andReturn(null)
            ->shouldReceive('getOption')
            ->with('formErrorsParagraph')
            ->andReturn(null);

        $form->shouldReceive('get')
            ->with('foo')
            ->andReturn($mockFoo);

        $mockFoo
            ->shouldReceive('getOption')
            ->with('short-label')
            ->andReturn(null)
            ->shouldReceive('getOption')
            ->with('error-message')
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
        $expected = '/(\s+)?<div class="validation-summary" role="alert" id="validationSummary">(\s+)?'
            . '<h3>form-errors-translated<\/h3>(\s+)?'
            . '<p><\/p>(\s+)?'
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
            ->andReturn($messages)
            ->shouldReceive('has')
            ->once()
            ->with('foo')
            ->andReturn(true)
            ->shouldReceive('getOption')
            ->with('formErrorsTitle')
            ->andReturn(null)
            ->shouldReceive('getOption')
            ->with('formErrorsParagraph')
            ->andReturn(null);

        $form->shouldReceive('get')
            ->with('foo')
            ->andReturn($mockFoo);

        $mockFoo
            ->shouldReceive('getOption')
            ->with('short-label')
            ->andReturn(null)
            ->shouldReceive('getOption')
            ->with('error-message')
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
        $expected = '/(\s+)?<div class="validation-summary" role="alert" id="validationSummary">(\s+)?'
            . '<h3>form-errors-translated<\/h3>(\s+)?'
            . '<p><\/p>(\s+)?'
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
            ->andReturn($messages)
            ->shouldReceive('has')
            ->once()
            ->with('foo')
            ->andReturn(true)
            ->shouldReceive('getOption')
            ->with('formErrorsTitle')
            ->andReturn(null)
            ->shouldReceive('getOption')
            ->with('formErrorsParagraph')
            ->andReturn(null);

        $form->shouldReceive('get')
            ->with('foo')
            ->andReturn($mockFoo);

        $mockFoo
            ->shouldReceive('getOption')
            ->with('short-label')
            ->andReturn(null)
            ->shouldReceive('getOption')
            ->with('error-message')
            ->andReturn(null)
            ->shouldReceive('getOption')
            ->with('fieldset-attributes')
            ->andReturn(['id' => 'foo-id']);

        $this->assertRegExp($expected, $sut($form));
    }

    /**
     * Test when a form element has been setup with a custom error message
     */
    public function testInvokeRenderWithCustomErrorMessage()
    {
        $messages = [
            'foo' => [
                'foo-error'
            ]
        ];
        $expected = '/(\s+)?<div class="validation-summary" role="alert" id="validationSummary">(\s+)?'
            . '<h3>form-errors-translated<\/h3>(\s+)?'
            . '<p><\/p>(\s+)?'
            . '<ol class="validation-summary__list">(\s+)?'
            . '<li class="validation-summary__item">(\s+)?foo-error-translated(\s+)?<\/li>(\s+)?'
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
            ->andReturn($messages)
            ->shouldReceive('getOption')
            ->andReturn(null)
            ->shouldReceive('getAttribute')
            ->andReturn(null)
            ->shouldReceive('has')
            ->once()
            ->with('foo')
            ->andReturn(true)
            ->shouldReceive('getOption')
            ->with('formErrorsTitle')
            ->andReturn(null)
            ->shouldReceive('getOption')
            ->with('formErrorsParagraph')
            ->andReturn(null);

        $form->shouldReceive('get')
            ->with('foo')
            ->andReturn($mockFoo);

        $mockFoo
            ->shouldReceive('getOption')
            ->with('short-label')
            ->andReturn(null)
            ->shouldReceive('getOption')
            ->with('error-message')
            ->andReturn('foo-error')
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

    public function testInvokeRenderWithShortLabelAndAnchor()
    {
        $messages = [
            'foo' => [
                'bar',
                'cake'
            ]
        ];
        $expected = '/(\s+)?<div class="validation-summary" role="alert" id="validationSummary">(\s+)?'
            . '<h3>form-errors-translated<\/h3>(\s+)?'
            . '<p><\/p>(\s+)?'
            . '<ol class="validation-summary__list">(\s+)?'
            . '<li class="validation-summary__item">(\s+)?'
            . '<a href="#foo-id">foo-label-translated\: bar-translated-translated<\/a>(\s+)?'
            . '<\/li>(\s+)?'
            . '<li class="validation-summary__item">(\s+)?'
            . '<a href="#foo-id">foo-label-translated\: cake-translated-translated<\/a>(\s+)?'
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
            ->andReturn($messages)
            ->shouldReceive('has')
            ->once()
            ->with('foo')
            ->andReturn(true)
            ->shouldReceive('getOption')
            ->with('formErrorsTitle')
            ->andReturn(null)
            ->shouldReceive('getOption')
            ->with('formErrorsParagraph')
            ->andReturn(null);

        $form->shouldReceive('get')
            ->with('foo')
            ->andReturn($mockFoo);

        $mockFoo
            ->shouldReceive('getOption')
            ->with('short-label')
            ->andReturn('foo-label')
            ->shouldReceive('getOption')
            ->with('error-message')
            ->andReturn(null)
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
        $expected = '/(\s+)?<div class="validation-summary" role="alert" id="validationSummary">(\s+)?'
            . '<h3>form-errors-translated<\/h3>(\s+)?'
            . '<p><\/p>(\s+)?'
            . '<ol class="validation-summary__list">(\s+)?'
            . '<li class="validation-summary__item">(\s+)?foo-label-translated\: bar-translated-translated(\s+)?'
            . '<\/li>(\s+)?'
            . '<li class="validation-summary__item">(\s+)?foo-label-translated\: cake-translated-translated(\s+)?'
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
            ->andReturn($messages)
            ->shouldReceive('has')
            ->once()
            ->with('foo')
            ->andReturn(true)
            ->shouldReceive('getOption')
            ->with('formErrorsTitle')
            ->andReturn(null)
            ->shouldReceive('getOption')
            ->with('formErrorsParagraph')
            ->andReturn(null);

        $form->shouldReceive('get')
            ->with('foo')
            ->andReturn($mockFoo);

        $mockFoo
            ->shouldReceive('getOption')
            ->with('short-label')
            ->andReturn('foo-label')
            ->shouldReceive('getOption')
            ->with('error-message')
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

    public function testInvokeRenderWithMessageObjet()
    {
        $mockValidationMessage = m::mock('\Common\Form\Elements\Validators\Messages\ValidationMessageInterface');
        $mockValidationMessage->shouldReceive('getMessage')
            ->andReturn('bar')
            ->shouldReceive('shouldTranslate')
            ->andReturn(true);

        $messages = [
            'foo' => [
                $mockValidationMessage
            ]
        ];
        $expected = '/(\s+)?<div class="validation-summary" role="alert" id="validationSummary">(\s+)?'
            . '<h3>form-errors-translated<\/h3>(\s+)?'
            . '<p><\/p>(\s+)?'
            . '<ol class="validation-summary__list">(\s+)?'
            . '<li class="validation-summary__item">(\s+)?bar-translated(\s+)?'
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
            ->andReturn($messages)
            ->shouldReceive('has')
            ->once()
            ->with('foo')
            ->andReturn(true)
            ->shouldReceive('getOption')
            ->with('formErrorsTitle')
            ->andReturn(null)
            ->shouldReceive('getOption')
            ->with('formErrorsParagraph')
            ->andReturn(null);

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

    /**
     * Test when a form element has been setup as a fieldset
     */
    public function testInvokeRenderWithMessageObjectElementAsFieldset()
    {
        $messages = [
            'foo' => [
                'bar',
                'cake'
            ]
        ];
        $expected = '/(\s+)?<div class="validation-summary" role="alert" id="validationSummary">(\s+)?'
            . '<h3>form-errors-translated<\/h3>(\s+)?'
            . '<p><\/p>(\s+)?'
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
            ->andReturn($messages)
            ->shouldReceive('getOption')
            ->andReturn(null)
            ->shouldReceive('getAttribute')
            ->andReturn(null)
            ->shouldReceive('has')
            ->times(3)
            ->andReturn(false)
            ->shouldReceive('getOption')
            ->with('formErrorsTitle')
            ->andReturn(null)
            ->shouldReceive('getOption')
            ->with('formErrorsParagraph')
            ->andReturn(null);

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

    public function testInvokeRenderWithMessagesWithAnchorAndCustomTitle()
    {
        $messages = [
            'foo' => [
                'bar',
                'cake'
            ]
        ];

        $title = 'error-title';
        $expected = '/(\s+)?<div class="validation-summary" role="alert" id="validationSummary">(\s+)?'
            . '<h3>error-title-translated<\/h3>(\s+)?'
            . '<p><\/p>(\s+)?'
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
            ->andReturn($messages)
            ->shouldReceive('has')
            ->once()
            ->with('foo')
            ->andReturn(true)
            ->shouldReceive('getOption')
            ->with('formErrorsTitle')
            ->andReturn($title)
            ->shouldReceive('getOption')
            ->with('formErrorsParagraph')
            ->andReturn(null);

        $form->shouldReceive('get')
            ->with('foo')
            ->andReturn($mockFoo);

        $mockFoo
            ->shouldReceive('getOption')
            ->with('short-label')
            ->andReturn(null)
            ->shouldReceive('getOption')
            ->with('error-message')
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

    public function testInvokeRenderWithMessagesWithAnchorAndCustomTitleAndParagraph()
    {
        $messages = [
            'foo' => [
                'bar',
                'cake'
            ]
        ];

        $title = 'error-title';
        $paragraph = 'error-paragraph';
        $expected = '/(\s+)?<div class="validation-summary" role="alert" id="validationSummary">(\s+)?'
            . '<h3>error-title-translated<\/h3>(\s+)?'
            . '<p>error-paragraph-translated<\/p>(\s+)?'
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
            ->andReturn($messages)
            ->shouldReceive('has')
            ->once()
            ->with('foo')
            ->andReturn(true)
            ->shouldReceive('getOption')
            ->with('formErrorsTitle')
            ->andReturn($title)
            ->shouldReceive('getOption')
            ->with('formErrorsParagraph')
            ->andReturn($paragraph);

        $form->shouldReceive('get')
            ->with('foo')
            ->andReturn($mockFoo);

        $mockFoo
            ->shouldReceive('getOption')
            ->with('short-label')
            ->andReturn(null)
            ->shouldReceive('getOption')
            ->with('error-message')
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

    public function testInvokeRenderWithMessagesWithAnchorAndCustomParagraph()
    {
        $messages = [
            'foo' => [
                'bar',
                'cake'
            ]
        ];

        $paragraph = 'error-paragraph';
        $expected = '/(\s+)?<div class="validation-summary" role="alert" id="validationSummary">(\s+)?'
            . '<h3>form-errors-translated<\/h3>(\s+)?'
            . '<p>error-paragraph-translated<\/p>(\s+)?'
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
            ->andReturn($messages)
            ->shouldReceive('has')
            ->once()
            ->with('foo')
            ->andReturn(true)
            ->shouldReceive('getOption')
            ->with('formErrorsTitle')
            ->andReturn(null)
            ->shouldReceive('getOption')
            ->with('formErrorsParagraph')
            ->andReturn($paragraph);

        $form->shouldReceive('get')
            ->with('foo')
            ->andReturn($mockFoo);

        $mockFoo
            ->shouldReceive('getOption')
            ->with('short-label')
            ->andReturn(null)
            ->shouldReceive('getOption')
            ->with('error-message')
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

    public function mockTranslate($text)
    {
        return $text . '-translated';
    }
}

