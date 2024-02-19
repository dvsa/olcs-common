<?php

namespace CommonTest\Form\View\Helper;

use Common\Form\Elements\Types\PostcodeSearch;
use Common\Form\Elements\Validators\Messages\FormElementMessageFormatter;
use Common\Form\Elements\Validators\Messages\FormElementMessageFormatterFactory;
use Common\Form\Elements\Validators\Messages\GenericValidationMessage;
use Common\Form\View\Helper\FormErrorsFactory;
use Common\Test\MocksServicesTrait;
use Interop\Container\ContainerInterface;
use Laminas\Form\Form;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\Mvc\I18n\Translator;
use Laminas\ServiceManager\ServiceManager;
use Laminas\Validator\ValidatorPluginManager;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Form\View\Helper\FormErrors;
use Laminas\Form\Element;
use Laminas\Form\Element\DateSelect;
use Mockery\MockInterface;
use HTMLPurifier;

/**
 * @see FormErrors
 */
class FormErrorsTest extends MockeryTestCase
{
    use MocksServicesTrait;

    protected const VALIDATOR_MANAGER = 'ValidatorManager';

    protected $sut;

    protected $view;

    /**
     * @test
     */
    public function __invoke_IsCallable()
    {
        // Setup
        $serviceLocator = $this->setUpServiceLocator();
        $sut = $this->setUpSut($serviceLocator);

        // Assert
        $this->assertIsCallable([$sut, '__invoke']);
    }

    /**
     * @test
     * @depends __invoke_IsCallable
     */
    public function __invoke_EscapesHtmlInMessage()
    {
        // Setup
        $serviceLocator = $this->setUpServiceLocator();
        $sut = $this->setUpSut($serviceLocator);
        $form = new Form();
        $form->setMessages(['<a>some text</a>']);

        // Execute
        $result = $sut->__invoke($form);

        // Assert
        $this->assertStringNotContainsString('<a>', $result);
    }

    /**
     * @test
     * @depends __invoke_IsCallable
     */
    public function __invoke_DoesNotPurifyMessageHtml_WhenMessageInterfaceHasEscapingDisabled()
    {
        // Setup
        $serviceLocator = $this->setUpServiceLocator();
        $purifier = $this->setUpMockService(HTMLPurifier::class);
        $serviceLocator->setService(HTMLPurifier::class, $purifier);
        $sut = $this->setUpSut($serviceLocator);
        $message = new GenericValidationMessage();
        $message->setMessage('bar');
        $message->setShouldEscape(false);
        $form = new Form();
        $form->setMessages(['foo' => $message]);

        // Set Expectations
        $purifier->shouldReceive('purify')->withAnyArgs()->andReturnUsing(fn($val) => $val)->never();

        // Execute
        $sut->render($form);
    }

    /**
     * @test
     * @depends __invoke_IsCallable
     */
    public function __invoke_WithoutForm()
    {
        $form = null;

        $sut = $this->sut;

        $this->assertSame($this->sut, $sut($form));
    }

    /**
     * @test
     * @depends __invoke_IsCallable
     */
    public function __invoke_RenderWithoutMessages()
    {
        $form = m::mock(\Laminas\Form\Form::class);
        $messages = [];
        $expected = '';

        $sut = $this->sut;

        // Expectations
        $form->shouldReceive('hasValidated')
            ->andReturn(true)
            ->shouldReceive('isValid')
            ->andReturn(false)
            ->shouldReceive('getMessages')
            ->andReturn($messages);

        $this->assertEquals($expected, $sut($form));
    }

    /**
     * @test
     * @depends __invoke_IsCallable
     */
    public function __invoke_RenderWithMessagesWithoutLabelOrAnchor()
    {
        $messages = [
            'foo' => [
                'bar',
                'cake'
            ]
        ];
        $expected = '/(\s+)?<div class="validation-summary" role="alert" id="validationSummary">(\s+)?'
            . '<h2 class="govuk-heading-m">form-errors-translated<\/h2>(\s+)?'
            . '<p><\/p>(\s+)?'
            . '<ol class="validation-summary__list">(\s+)?'
            . '<li class="validation-summary__item">(\s+)?Bar-translated(\s+)?<\/li>(\s+)?'
            . '<li class="validation-summary__item">(\s+)?Cake-translated(\s+)?<\/li>(\s+)?'
            . '<\/ol>(\s+)?'
            . '<\/div>/';

        $sut = $this->sut;

        // Mocks
        $form = m::mock(\Laminas\Form\Form::class);

        $element = $this->setUpElement();

        // Expectations
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
            ->once()
            ->with('formErrorsTitle')
            ->andReturn(null)
            ->shouldReceive('getOption')
            ->once()
            ->with('formErrorsParagraph')
            ->andReturn(null);

        $form->shouldReceive('get')
            ->with('foo')
            ->andReturn($element);

        $this->assertMatchesRegularExpression($expected, $sut($form));
    }

    /**
     * @test
     * @depends __invoke_IsCallable
     */
    public function __invoke_RenderWithMessagesWithAnchor()
    {
        $messages = [
            'foo' => [
                'bar',
                'cake'
            ]
        ];
        $expected = '/(\s+)?<div class="validation-summary" role="alert" id="validationSummary">(\s+)?'
            . '<h2 class="govuk-heading-m">form-errors-translated<\/h2>(\s+)?'
            . '<p><\/p>(\s+)?'
            . '<ol class="validation-summary__list">(\s+)?'
            . '<li class="validation-summary__item">(\s+)?<a href="#foo-id">Bar-translated<\/a>(\s+)?<\/li>(\s+)?'
            . '<li class="validation-summary__item">(\s+)?<a href="#foo-id">Cake-translated<\/a>(\s+)?<\/li>(\s+)?'
            . '<\/ol>(\s+)?'
            . '<\/div>/';

        $sut = $this->sut;

        // Mocks
        $form = m::mock(\Laminas\Form\Form::class);

        $element = $this->setUpElement();
        $element->setLabel('foo');
        $element->setAttribute('id', 'foo-id');

        // Expectations
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
            ->once()
            ->with('formErrorsTitle')
            ->andReturn(null)
            ->shouldReceive('getOption')
            ->once()
            ->with('formErrorsParagraph')
            ->andReturn(null);

        $form->shouldReceive('get')
            ->with('foo')
            ->andReturn($element);

        $this->assertMatchesRegularExpression($expected, $sut($form));
    }

    /**
     * @test
     * @depends __invoke_IsCallable
     */
    public function __invoke_RenderWithMessagesWithAnchor2()
    {
        $messages = [
            'foo' => [
                'bar',
                'cake'
            ]
        ];
        $expected = '/(\s+)?<div class="validation-summary" role="alert" id="validationSummary">(\s+)?'
            . '<h2 class="govuk-heading-m">form-errors-translated<\/h2>(\s+)?'
            . '<p><\/p>(\s+)?'
            . '<ol class="validation-summary__list">(\s+)?'
            . '<li class="validation-summary__item">(\s+)?<a href="#foo-id">Bar-translated<\/a>(\s+)?<\/li>(\s+)?'
            . '<li class="validation-summary__item">(\s+)?<a href="#foo-id">Cake-translated<\/a>(\s+)?<\/li>(\s+)?'
            . '<\/ol>(\s+)?'
            . '<\/div>/';

        $sut = $this->sut;

        // Mocks
        $form = m::mock(\Laminas\Form\Form::class);

        $element = $this->setUpElement();
        $element->setOption('label_attributes', ['id' => 'foo-id']);

        // Expectations
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
            ->once()
            ->with('formErrorsTitle')
            ->andReturn(null)
            ->shouldReceive('getOption')
            ->once()
            ->with('formErrorsParagraph')
            ->andReturn(null);

        $form->shouldReceive('get')
            ->with('foo')
            ->andReturn($element);

        $this->assertMatchesRegularExpression($expected, $sut($form));
    }

    /**
     * @test
     * @depends __invoke_IsCallable
     */
    public function __invoke_RenderWithMessagesWithAnchor3()
    {
        $messages = [
            'foo' => [
                'bar',
                'cake'
            ]
        ];
        $expected = '/(\s+)?<div class="validation-summary" role="alert" id="validationSummary">(\s+)?'
            . '<h2 class="govuk-heading-m">form-errors-translated<\/h2>(\s+)?'
            . '<p><\/p>(\s+)?'
            . '<ol class="validation-summary__list">(\s+)?'
            . '<li class="validation-summary__item">(\s+)?<a href="#foo-id">Bar-translated<\/a>(\s+)?<\/li>(\s+)?'
            . '<li class="validation-summary__item">(\s+)?<a href="#foo-id">Cake-translated<\/a>(\s+)?<\/li>(\s+)?'
            . '<\/ol>(\s+)?'
            . '<\/div>/';

        $sut = $this->sut;

        // Mocks
        $form = m::mock(\Laminas\Form\Form::class);

        $element = $this->setUpElement();
        $element->setOption('fieldset-attributes', ['id' => 'foo-id']);

        // Expectations
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
            ->once()
            ->with('formErrorsTitle')
            ->andReturn(null)
            ->shouldReceive('getOption')
            ->once()
            ->with('formErrorsParagraph')
            ->andReturn(null);

        $form->shouldReceive('get')
            ->with('foo')
            ->andReturn($element);

        $this->assertMatchesRegularExpression($expected, $sut($form));
    }

    /**
     * @test
     * @depends __invoke_IsCallable
     */
    public function __invoke_RenderWithMessagesWithAnchorPostcodeSearch()
    {
        $messages = [
            'foo' => [
                'bar',
            ]
        ];
        $expected = '/(\s+)?<div class="validation-summary" role="alert" id="validationSummary">(\s+)?'
            . '<h2 class="govuk-heading-m">form-errors-translated<\/h2>(\s+)?'
            . '<p><\/p>(\s+)?'
            . '<ol class="validation-summary__list">(\s+)?'
            . '<li class="validation-summary__item">(\s+)?<a href="#PC_ID">Bar-translated<\/a>(\s+)?<\/li>(\s+)?'
            . '<\/ol>(\s+)?'
            . '<\/div>/';

        $sut = $this->sut;

        // Mocks
        $form = m::mock(\Laminas\Form\Form::class)->makePartial();
        $mockFoo = m::mock(PostcodeSearch::class)->makePartial();

        // Expectations
        $form->shouldReceive('hasValidated')->andReturn(true)
            ->shouldReceive('isValid')->andReturn(false)
            ->shouldReceive('getMessages')->andReturn($messages)
            ->shouldReceive('has')->once()->with('foo')->andReturn(true)
            ->shouldReceive('get')->with('foo')->once()->andReturn($mockFoo);

        $mockFoo
            ->shouldReceive('has')->once()->andReturn()
            ->shouldReceive('get')->with('postcode')->twice()->andReturn(
                m::mock()->shouldReceive('getAttribute')->with('id')->twice()->andReturn('PC_ID')->getMock()
            )
            ->shouldReceive('getLabel')->andReturn('Default Label');

        $this->assertMatchesRegularExpression($expected, $sut($form));
    }

    /**
     * @test
     * @depends __invoke_IsCallable
     */
    public function __invoke_RenderWithMessagesWithAnchorDateSelect()
    {
        $messages = [
            'foo' => [
                'bar',
            ]
        ];
        $expected = '/(\s+)?<div class="validation-summary" role="alert" id="validationSummary">(\s+)?'
            . '<h2 class="govuk-heading-m">form-errors-translated<\/h2>(\s+)?'
            . '<p><\/p>(\s+)?'
            . '<ol class="validation-summary__list">(\s+)?'
            . '<li class="validation-summary__item">(\s+)?<a href="#DS_ID_day">Bar-translated<\/a>(\s+)?<\/li>(\s+)?'
            . '<\/ol>(\s+)?'
            . '<\/div>/';

        $sut = $this->sut;

        // Mocks
        $form = m::mock(\Laminas\Form\Form::class)->makePartial();
        $element = (new DateSelect())->setAttribute('id', 'DS_ID');
        $element->setLabel('Default Label');

        // Expectations
        $form->shouldReceive('getMessages')->andReturn($messages)
            ->shouldReceive('has')->once()->with('foo')->andReturn(true)
            ->shouldReceive('get')->with('foo')->andReturn($element);

        $this->assertMatchesRegularExpression($expected, $sut($form));
    }

    /**
     * @test
     * @depends __invoke_IsCallable
     */
    public function __invoke_RenderWithMessagesWithAnchorUsingName()
    {
        $messages = [
            'foo' => [
                'bar',
            ]
        ];
        $expected = '/(\s+)?<div class="validation-summary" role="alert" id="validationSummary">(\s+)?'
            . '<h2 class="govuk-heading-m">form-errors-translated<\/h2>(\s+)?'
            . '<p><\/p>(\s+)?'
            . '<ol class="validation-summary__list">(\s+)?'
            . '<li class="validation-summary__item">(\s+)?<a href="#NAME">Bar-translated<\/a>(\s+)?<\/li>(\s+)?'
            . '<\/ol>(\s+)?'
            . '<\/div>/';

        $sut = $this->sut;

        // Mocks
        $form = m::mock(\Laminas\Form\Form::class)->makePartial();
        $element = $this->setUpElement('NAME');

        // Expectations
        $form->shouldReceive('getMessages')->andReturn($messages)
            ->shouldReceive('has')->once()->with('foo')->andReturn(true)
            ->shouldReceive('get')->with('foo')->andReturn($element);

        $this->assertMatchesRegularExpression($expected, $sut($form));
    }

    /**
     * @test
     * @testdox Test when a form element has been setup with a custom error message
     * @depends __invoke_IsCallable
     */
    public function __invoke_RenderWithCustomErrorMessage()
    {
        $messages = [
            'foo' => [
                'foo-error'
            ]
        ];
        $expected = '/(\s+)?<div class="validation-summary" role="alert" id="validationSummary">(\s+)?'
            . '<h2 class="govuk-heading-m">form-errors-translated<\/h2>(\s+)?'
            . '<p><\/p>(\s+)?'
            . '<ol class="validation-summary__list">(\s+)?'
            . '<li class="validation-summary__item">(\s+)?Foo-error-translated(\s+)?<\/li>(\s+)?'
            . '<\/ol>(\s+)?'
            . '<\/div>/';

        $sut = $this->sut;

        // Mocks
        $form = m::mock(\Laminas\Form\Form::class);
        $element = $this->setUpElement();
        $element->setOption('error-message', 'foo-error');

        // Expectations
        $form->shouldReceive('hasValidated')
            ->andReturn(true)
            ->shouldReceive('isValid')
            ->andReturn(false)
            ->shouldReceive('getMessages')
            ->andReturn($messages)
            ->shouldReceive('getOption')
            ->once()
            ->with('formErrorsTitle')
            ->andReturn(null)
            ->shouldReceive('getOption')
            ->once()
            ->with('formErrorsParagraph')
            ->andReturn(null)
            ->shouldReceive('getOption')
            ->andReturn(null)
            ->shouldReceive('getAttribute')
            ->andReturn(null)
            ->shouldReceive('has')
            ->once()
            ->with('foo')
            ->andReturn(true);

        $form->shouldReceive('get')
            ->with('foo')
            ->andReturn($element);

        $this->assertMatchesRegularExpression($expected, $sut($form));
    }

    /**
     * @test
     * @depends __invoke_IsCallable
     */
    public function __invoke_RenderWithShortLabelAndAnchor()
    {
        $messages = [
            'foo' => [
                'bar',
                'cake'
            ]
        ];
        $expected = '/(\s+)?<div class="validation-summary" role="alert" id="validationSummary">(\s+)?'
            . '<h2 class="govuk-heading-m">form-errors-translated<\/h2>(\s+)?'
            . '<p><\/p>(\s+)?'
            . '<ol class="validation-summary__list">(\s+)?'
            . '<li class="validation-summary__item">(\s+)?'
            . '<a href="#foo-id">Foo-label-translated\: bar-translated-translated<\/a>(\s+)?'
            . '<\/li>(\s+)?'
            . '<li class="validation-summary__item">(\s+)?'
            . '<a href="#foo-id">Foo-label-translated\: cake-translated-translated<\/a>(\s+)?'
            . '<\/li>(\s+)?'
            . '<\/ol>(\s+)?'
            . '<\/div>/';

        $sut = $this->sut;

        // Mocks
        $form = m::mock(\Laminas\Form\Form::class);

        $element = $this->setUpElement();
        $element->setOption('short-label', 'foo-label');
        $element->setOption('fieldset-attributes', ['id' => 'foo-id']);

        // Expectations
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
            ->once()
            ->with('formErrorsTitle')
            ->andReturn(null)
            ->shouldReceive('getOption')
            ->once()
            ->with('formErrorsParagraph')
            ->andReturn(null);

        $form->shouldReceive('get')
            ->with('foo')
            ->andReturn($element);

        $this->assertMatchesRegularExpression($expected, $sut($form));
    }

    /**
     * @test
     * @depends __invoke_IsCallable
     */
    public function __invoke_RenderWithShortLabelWithoutAnchor()
    {
        $messages = [
            'foo' => [
                'bar',
                'cake'
            ]
        ];
        $expected = '/(\s+)?<div class="validation-summary" role="alert" id="validationSummary">(\s+)?'
            . '<h2 class="govuk-heading-m">form-errors-translated<\/h2>(\s+)?'
            . '<p><\/p>(\s+)?'
            . '<ol class="validation-summary__list">(\s+)?'
            . '<li class="validation-summary__item">(\s+)?Foo-label-translated\: bar-translated-translated(\s+)?'
            . '<\/li>(\s+)?'
            . '<li class="validation-summary__item">(\s+)?Foo-label-translated\: cake-translated-translated(\s+)?'
            . '<\/li>(\s+)?'
            . '<\/ol>(\s+)?'
            . '<\/div>/';

        $sut = $this->sut;

        // Mocks
        $form = m::mock(\Laminas\Form\Form::class);
        $element = $this->setUpElement();
        $element->setOption('short-label', 'foo-label');

        // Expectations
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
            ->once()
            ->with('formErrorsTitle')
            ->andReturn(null)
            ->shouldReceive('getOption')
            ->once()
            ->with('formErrorsParagraph')
            ->andReturn(null);

        $form->shouldReceive('get')
            ->with('foo')
            ->andReturn($element);

        $this->assertMatchesRegularExpression($expected, $sut($form));
    }

    /**
     * @test
     * @depends __invoke_IsCallable
     */
    public function __invoke_RenderWithMessageObjet()
    {
        $mockValidationMessage = new GenericValidationMessage();
        $mockValidationMessage->setMessage('bar');
        $mockValidationMessage->setShouldTranslate(true);

        $messages = [
            'foo' => [
                $mockValidationMessage
            ]
        ];
        $expected = '/(\s+)?<div class="validation-summary" role="alert" id="validationSummary">(\s+)?'
            . '<h2 class="govuk-heading-m">form-errors-translated<\/h2>(\s+)?'
            . '<p><\/p>(\s+)?'
            . '<ol class="validation-summary__list">(\s+)?'
            . '<li class="validation-summary__item">(\s+)?Bar-translated(\s+)?'
            . '<\/li>(\s+)?'
            . '<\/ol>(\s+)?'
            . '<\/div>/';

        $sut = $this->sut;

        // Mocks
        $form = m::mock(\Laminas\Form\Form::class);
        $element = $this->setUpElement();

        // Expectations
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
            ->once()
            ->with('formErrorsTitle')
            ->andReturn(null)
            ->shouldReceive('getOption')
            ->once()
            ->with('formErrorsParagraph')
            ->andReturn(null);

        $form->shouldReceive('get')
            ->with('foo')
            ->andReturn($element);

        $this->assertMatchesRegularExpression($expected, $sut($form));
    }

    /**
     * @test
     * @testdox Test when a form element has been setup as a fieldset
     * @depends __invoke_IsCallable
     */
    public function __invoke_RenderWithMessageObjectElementAsFieldset()
    {
        $messages = [
            'foo' => [
                'bar',
                'cake'
            ]
        ];
        $expected = '/(\s+)?<div class="validation-summary" role="alert" id="validationSummary">(\s+)?'
            . '<h2 class="govuk-heading-m">form-errors-translated<\/h2>(\s+)?'
            . '<p><\/p>(\s+)?'
            . '<ol class="validation-summary__list">(\s+)?'
            . '<li class="validation-summary__item">(\s+)?Bar-translated(\s+)?<\/li>(\s+)?'
            . '<li class="validation-summary__item">(\s+)?Cake-translated(\s+)?<\/li>(\s+)?'
            . '<\/ol>(\s+)?'
            . '<\/div>/';

        $sut = $this->sut;

        // Mocks
        $form = m::mock(\Laminas\Form\Form::class);

        // Expectations
        $form->shouldReceive('hasValidated')
            ->andReturn(true)
            ->shouldReceive('isValid')
            ->andReturn(false)
            ->shouldReceive('getMessages')
            ->andReturn($messages)
            ->shouldReceive('getOption')
            ->once()
            ->with('formErrorsTitle')
            ->andReturn(null)
            ->shouldReceive('getOption')
            ->once()
            ->with('formErrorsParagraph')
            ->andReturn(null)
            ->shouldReceive('getOption')
            ->andReturn(null)
            ->shouldReceive('getAttribute')
            ->andReturn(null)
            ->shouldReceive('has')
            ->times(3)
            ->andReturn(false)
            ->shouldReceive('getName')->andReturn(null)
            ->shouldReceive('getLabel')->andReturn('Default Label');

        $this->assertMatchesRegularExpression($expected, $sut($form));
    }

    /**
     * @test
     * @depends __invoke_IsCallable
     */
    public function __invoke_RenderWithMessagesWithAnchorAndCustomTitle()
    {
        $messages = [
            'foo' => [
                'bar',
                'cake'
            ]
        ];

        $title = 'error-title';
        $expected = '/(\s+)?<div class="validation-summary" role="alert" id="validationSummary">(\s+)?'
            . '<h2 class="govuk-heading-m">error-title-translated<\/h2>(\s+)?'
            . '<p><\/p>(\s+)?'
            . '<ol class="validation-summary__list">(\s+)?'
            . '<li class="validation-summary__item">(\s+)?<a href="#foo-id">Bar-translated<\/a>(\s+)?<\/li>(\s+)?'
            . '<li class="validation-summary__item">(\s+)?<a href="#foo-id">Cake-translated<\/a>(\s+)?<\/li>(\s+)?'
            . '<\/ol>(\s+)?'
            . '<\/div>/';

        $sut = $this->sut;

        // Mocks
        $form = m::mock(\Laminas\Form\Form::class);
        $element = $this->setUpElement();
        $element->setAttribute('id', 'foo-id');

        // Expectations
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
            ->twice()
            ->with('formErrorsTitle')
            ->andReturn($title)
            ->shouldReceive('getOption')
            ->once()
            ->with('formErrorsParagraph')
            ->andReturn(null);

        $form->shouldReceive('get')
            ->with('foo')
            ->andReturn($element);

        $this->assertMatchesRegularExpression($expected, $sut($form));
    }

    /**
     * @test
     * @depends __invoke_IsCallable
     */
    public function __invoke_RenderWithMessagesWithAnchorAndCustomTitleAndParagraph()
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
            . '<h2 class="govuk-heading-m">error-title-translated<\/h2>(\s+)?'
            . '<p>error-paragraph-translated<\/p>(\s+)?'
            . '<ol class="validation-summary__list">(\s+)?'
            . '<li class="validation-summary__item">(\s+)?<a href="#foo-id">Bar-translated<\/a>(\s+)?<\/li>(\s+)?'
            . '<li class="validation-summary__item">(\s+)?<a href="#foo-id">Cake-translated<\/a>(\s+)?<\/li>(\s+)?'
            . '<\/ol>(\s+)?'
            . '<\/div>/';

        $sut = $this->sut;

        // Mocks
        $form = m::mock(\Laminas\Form\Form::class);
        $element = $this->setUpElement();
        $element->setAttribute('id', 'foo-id');

        // Expectations
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
            ->twice()
            ->with('formErrorsTitle')
            ->andReturn($title)
            ->shouldReceive('getOption')
            ->twice()
            ->with('formErrorsParagraph')
            ->andReturn($paragraph);

        $form->shouldReceive('get')
            ->with('foo')
            ->andReturn($element);

        $this->assertMatchesRegularExpression($expected, $sut($form));
    }

    /**
     * @test
     * @depends __invoke_IsCallable
     */
    public function __invoke_RenderWithMessagesWithAnchorAndCustomParagraph()
    {
        $messages = [
            'foo' => [
                'bar',
                'cake'
            ]
        ];

        $paragraph = 'error-paragraph';
        $expected = '/(\s+)?<div class="validation-summary" role="alert" id="validationSummary">(\s+)?'
            . '<h2 class="govuk-heading-m">form-errors-translated<\/h2>(\s+)?'
            . '<p>error-paragraph-translated<\/p>(\s+)?'
            . '<ol class="validation-summary__list">(\s+)?'
            . '<li class="validation-summary__item">(\s+)?<a href="#foo-id">Bar-translated<\/a>(\s+)?<\/li>(\s+)?'
            . '<li class="validation-summary__item">(\s+)?<a href="#foo-id">Cake-translated<\/a>(\s+)?<\/li>(\s+)?'
            . '<\/ol>(\s+)?'
            . '<\/div>/';

        $sut = $this->sut;

        // Mocks
        $form = m::mock(\Laminas\Form\Form::class);
        $element = $this->setUpElement();
        $element->setAttribute('id', 'foo-id');

        // Expectations
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
            ->once()
            ->with('formErrorsTitle')
            ->andReturn(null)
            ->shouldReceive('getOption')
            ->twice()
            ->with('formErrorsParagraph')
            ->andReturn($paragraph);

        $form->shouldReceive('get')
            ->with('foo')
            ->andReturn($element);

        $this->assertMatchesRegularExpression($expected, $sut($form));
    }

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        $this->view = m::mock(\Laminas\View\Renderer\RendererInterface::class);
        $serviceLocator = $this->setUpServiceLocator();
        $this->sut = $this->setUpSut($serviceLocator);
        $this->sut->setView($this->view);
    }

    protected function setUpSut(ContainerInterface $serviceLocator): FormErrors
    {
        $pluginManager = $this->setUpAbstractPluginManager($serviceLocator);
        return (new FormErrorsFactory())->__invoke($pluginManager, FormErrors::class);
    }

    /**
     * @param ServiceManager $serviceManager
     */
    protected function setUpDefaultServices(ServiceManager $serviceManager)
    {
        $serviceManager->setService(TranslatorInterface::class, $this->setUpTranslator());
        $serviceManager->setFactory(FormElementMessageFormatter::class, new FormElementMessageFormatterFactory());
        $serviceManager->setService(static::VALIDATOR_MANAGER, new ValidatorPluginManager());
    }

    /**
     * @return MockInterface|Translator
     */
    protected function setUpTranslator(): MockInterface
    {
        $instance = $this->setUpMockService(Translator::class);
        $instance->shouldReceive('translate')->andReturnUsing(fn($key) => $key . '-translated')->byDefault();
        return $instance;
    }

    /**
     * @param string|null $name
     * @return Element
     */
    protected function setUpElement(string $name = null): Element
    {
        $element = new Element($name);
        $element->setLabel('Default Label');
        return $element;
    }
}
