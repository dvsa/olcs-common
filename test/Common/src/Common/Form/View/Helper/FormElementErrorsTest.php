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
use Laminas\Form\Element;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\Mvc\I18n\Translator;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\ServiceManager\ServiceManager;
use Laminas\View\HelperPluginManager;
use Laminas\View\Renderer\PhpRenderer;
use Mockery\MockInterface;
use HTMLPurifier;

/**
 * @see FormElementErrors
 */
class FormElementErrorsTest extends MockeryTestCase
{
    use MocksServicesTrait;

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
        $translator = new \Laminas\I18n\Translator\Translator();
        $translateHelper = new \Laminas\I18n\View\Helper\Translate();
        $translateHelper->setTranslator($translator);

        $helpers = new HelperPluginManager();
        $helpers->setService('translate', $translateHelper);

        $view = new PhpRenderer();
        $view->setHelperPluginManager($helpers);

        $serviceLocator = $this->setUpServiceLocator();
        $viewHelper = $this->setUpSut($serviceLocator);
        $viewHelper->setView($view);
        $markup = $viewHelper($element);

        $this->assertSame('<p class="error__text">Message</p>', $markup);
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

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return FormElementErrors
     */
    protected function setUpSut(ServiceLocatorInterface $serviceLocator): FormElementErrors
    {
        $pluginManager = $this->setUpAbstractPluginManager($serviceLocator);
        $instance = (new FormElementErrorsFactory())->createService($pluginManager);
        return $instance;
    }

    /**
     * @return MockInterface|Translator
     */
    protected function setUpTranslator(): MockInterface
    {
        $instance = $this->setUpMockService(Translator::class);
        $instance->shouldReceive('translate')->andReturnUsing(function ($key) {
            return $key;
        })->byDefault();
        return $instance;
    }

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return FormLabel
     */
    protected function setUpFormLabel(ServiceLocatorInterface $serviceLocator): FormLabel
    {
        $instance = (new FormLabelFactory())->createService($serviceLocator);
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
    }
}
