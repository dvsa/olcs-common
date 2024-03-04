<?php

namespace CommonTest\Form\View\Helper;

use Common\Form\Elements\Validators\Messages\FormElementMessageFormatter;
use Common\Form\Elements\Validators\Messages\FormElementMessageFormatterFactory;
use Common\Form\View\Helper\Extended\FormLabel;
use Common\Form\View\Helper\Extended\FormLabelFactory;
use Common\Form\View\Helper\FormElementErrors;
use Common\Form\View\Helper\FormElementErrorsFactory;
use Common\Test\MockeryTestCase;
use Common\Test\MocksServicesTrait;
use HTMLPurifier;
use Laminas\Form\Element;
use Laminas\I18n\Translator\Translator;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\I18n\View\Helper\Translate;
use Laminas\ServiceManager\ServiceManager;
use Laminas\Validator\ValidatorPluginManager;
use Laminas\View\HelperPluginManager;
use Laminas\View\Renderer\PhpRenderer;
use Mockery\MockInterface;
use Psr\Container\ContainerInterface;

/**
 * @see FormElementErrors
 */
class FormElementErrorsTest extends MockeryTestCase
{
    use MocksServicesTrait;

    protected const VALIDATOR_MANAGER = 'ValidatorManager';

    /**
     * @test
     */
    public function render_IsCallable()
    {
        // Setup
        $serviceLocator = $this->setUpServiceLocator();
        $sut = $this->setUpSut($serviceLocator);

        // Assert
        $this->assertIsCallable([$sut, 'render']);
    }

    /**
     * @test
     * @depends render_IsCallable
     */
    public function render_EscapesHtmlInMessage()
    {
        // Setup
        $serviceLocator = $this->setUpServiceLocator();
        $sut = $this->setUpSut($serviceLocator);
        $element = new Element();
        $element->setMessages(['<a>some text</a>']);

        // Execute
        $result = $sut->render($element);

        // Assert
        $this->assertStringNotContainsString('<a>', $result);
    }

    /**
     * @depends render_IsCallable
     */
    public function testRender()
    {
        $element = new \Laminas\Form\Element\Text('test');
        $element->setLabel('Test');
        $element->setMessages(['Message']);
        $translator = new Translator();
        $translateHelper = new Translate();
        $translateHelper->setTranslator($translator);

        $container = m::mock(ContainerInterface::class);
        $helpers = new HelperPluginManager($container);
        $helpers->setService('translate', $translateHelper);

        $view = new PhpRenderer();
        $view->setHelperPluginManager($helpers);

        $serviceLocator = $this->setUpServiceLocator();
        $viewHelper = $this->setUpSut($serviceLocator);
        $viewHelper->setView($view);
        $markup = $viewHelper($element);

        $expectedMarkup = '<p class="govuk-error-message"><span class="govuk-visually-hidden">Error:</span>Message</p>';

        $this->assertSame($expectedMarkup, $markup);
    }

    /**
     * @return Element
     */
    protected function setUpElement(): Element
    {
        $element = new Element();
        $element->setAttribute('id', 'foo');
        $element->setLabel("foo");
        return $element;
    }

    protected function setUpSut(ContainerInterface $container): FormElementErrors
    {
        $pluginManager = $this->setUpAbstractPluginManager($container);
        $instance = (new FormElementErrorsFactory())->__invoke($pluginManager, FormElementErrors::class);
        return $instance;
    }

    /**
     * @return MockInterface|Translator
     */
    protected function setUpTranslator(): MockInterface
    {
        $instance = $this->setUpMockService(Translator::class);
        $instance->shouldReceive('translate')->andReturnUsing(fn($key) => $key)->byDefault();
        return $instance;
    }

    protected function setUpFormLabel(ContainerInterface $container): FormLabel
    {
        $instance = (new FormLabelFactory())->__invoke($container, FormLabel::class);
        return $instance;
    }

    /**
     * @param ServiceManager $serviceManager
     */
    protected function setUpDefaultServices(ServiceManager $serviceManager)
    {
        $serviceManager->setService(TranslatorInterface::class, $this->setUpTranslator());
        $serviceManager->setService(HTMLPurifier::class, new HTMLPurifier());
        $serviceManager->setFactory(FormLabel::class, new FormLabelFactory());
        $serviceManager->setFactory(FormElementMessageFormatter::class, new FormElementMessageFormatterFactory());
        $serviceManager->setService(static::VALIDATOR_MANAGER, new ValidatorPluginManager());
    }
}
