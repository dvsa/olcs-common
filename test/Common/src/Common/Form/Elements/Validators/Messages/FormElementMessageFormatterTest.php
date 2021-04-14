<?php

namespace CommonTest\Form\Elements\Validators\Messages;

use Common\Form\Elements\Validators\Messages\FormElementMessageFormatter;
use Common\Form\Elements\Validators\Messages\FormElementMessageFormatterFactory;
use Common\Form\Elements\Validators\Messages\GenericValidationMessage;
use Common\Form\View\Helper\Extended\FormLabel;
use Common\Form\View\Helper\Extended\FormLabelFactory;
use Common\Test\MockeryTestCase;
use Common\Test\MocksServicesTrait;
use Laminas\Form\Element;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\Mvc\I18n\Translator;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\ServiceManager\ServiceManager;
use Laminas\Validator\NotEmpty;
use Mockery\MockInterface;
use HTMLPurifier;

class FormElementMessageFormatterTest extends MockeryTestCase
{
    use MocksServicesTrait;

    /**
     * @test
     */
    public function formatElementMessage_IsCallable()
    {
        $serviceLocator = $this->setUpServiceLocator();
        $sut = $this->setUpSut($serviceLocator);
        $this->assertIsCallable([$sut, 'formatElementMessage']);
    }

    /**
     * @test
     * @depends formatElementMessage_IsCallable
     */
    public function formatElementMessage_ReturnsString()
    {
        //setup
        $serviceLocator = $this->setUpServiceLocator();
        $sut = $this->setUpSut($serviceLocator);
        $element = $this->setUpElement();

        //Execute
        $result = $sut->formatElementMessage($element, 'foo', 'foo');

        //Assert
        $this->assertIsString($result);
    }

    /**
     * @test
     * @testdox Format element message should accept null labels from an element; we have some dodgy elements which do
     * not always adhere to the element interface by returning a string as a label.
     * @depends formatElementMessage_ReturnsString
     */
    public function formatElementMessage_AcceptsNullElementLabels()
    {
        //setup
        $serviceLocator = $this->setUpServiceLocator();
        $sut = $this->setUpSut($serviceLocator);
        $element = new class extends Element {
            public function getLabel()
            {
                return null;
            }
        };

        //Execute
        $result = $sut->formatElementMessage($element, 'foo', 'foo');

        //Assert
        $this->assertIsString($result);
    }

    /**
     * @test
     * @depends formatElementMessage_IsCallable
     */
    public function formatElementMessage_ReplacesFieldLabelsPlaceholder()
    {
        //setup
        $serviceLocator = $this->setUpServiceLocator();
        $sut = $this->setUpSut($serviceLocator);
        $element = $this->setUpElement();

        //Execute
        $result = $sut->formatElementMessage($element, 'Enter ' . FormElementMessageFormatter::FIELD_LABEL_PLACEHOLDER, 'foo');

        //Assert
        $this->assertStringNotContainsString(FormElementMessageFormatter::FIELD_LABEL_PLACEHOLDER, $result);
    }

    /**
     * @test
     * @depends formatElementMessage_IsCallable
     */
    public function formatElementMessage_ReplacesFieldLabelPlaceholder_WhenLabelEmpty_WithEmptyString()
    {
        // Setup
        $serviceLocator = $this->setUpServiceLocator();
        $sut = $this->setUpSut($serviceLocator);
        $element = $this->setUpElement();
        $element->setLabel('');

        // Execute
        $result = $sut->formatElementMessage($element, 'Enter ' . FormElementMessageFormatter::FIELD_LABEL_PLACEHOLDER, 'foo');

        // Assert
        $this->assertStringContainsString('Enter ', $result);
    }

    /**
     * @test
     * @depends formatElementMessage_ReplacesFieldLabelsPlaceholder
     */
    public function formatElementMessage_ReplacesFieldLabelPlaceholder_WithLabel()
    {
        // Setup
        $serviceLocator = $this->setUpServiceLocator();
        $labelRenderer = $this->setUpMockService(FormLabel::class);
        $serviceLocator->setService(FormLabel::class, $labelRenderer);
        $sut = $this->setUpSut($serviceLocator);
        $element = $this->setUpElement();
        $element->setLabel($expectedLabel = 'hello');

        // Execute
        $result = $sut->formatElementMessage($element, 'Enter ' . FormElementMessageFormatter::FIELD_LABEL_PLACEHOLDER, 'foo');

        // Assert
        $this->assertStringContainsString("Enter " . $expectedLabel, $result);
    }

    /**
     * @test
     * @depends formatElementMessage_ReplacesFieldLabelPlaceholder_WithLabel
     */
    public function formatElementMessage_ReplacesFieldLabelPlaceholder_WithLabel_AsTrimmed()
    {
        //setup
        $serviceLocator = $this->setUpServiceLocator();
        $labelRenderer = $this->setUpMockService(FormLabel::class);
        $serviceLocator->setService(FormLabel::class, $labelRenderer);
        $sut = $this->setUpSut($serviceLocator);
        $element = $this->setUpElement();
        $element->setLabel('     foo');

        //Execute
        $result = $sut->formatElementMessage($element, 'Enter ' . FormElementMessageFormatter::FIELD_LABEL_PLACEHOLDER, 'foo');

        //Assert
        $this->assertStringContainsString("Enter foo", $result);
    }

    /**
     * @test
     * @depends formatElementMessage_ReplacesFieldLabelPlaceholder_WithLabel
     * @testdox Replaces the field label placeholder with a label that is only translated once. It is important that the
     * replacement message is not translated a second time before having any variables replaced. This is because, at the
     * time of writing this, there is an issue with the MissingTranslationProcessor which will change the placeholder
     * prefix/suffix curly braces so that they no longer get correctly replaced.
     */
    public function formatElementMessage_ReplacesFieldLabelPlaceholder_WithLabel_TranslatedOnce()
    {
        // Setup
        $serviceLocator = $this->setUpServiceLocator();
        $sut = $this->setUpSut($serviceLocator);
        $element = $this->setUpElement();
        $element->setLabel('bar');
        $element->setAttribute('type', $elementType = 'password');
        $messageKey = NotEmpty::IS_EMPTY;
        $this->resolveMockService($serviceLocator, TranslatorInterface::class)
            ->shouldReceive('translate')
            ->with(sprintf('validation.element.%s.%s', $elementType, $messageKey))
            ->andReturn($translatedReplacementDefaultMessage = 'foo');

        // Define Expectations
        $this->resolveMockService($serviceLocator, TranslatorInterface::class)
            ->shouldReceive('translate')
            ->with($translatedReplacementDefaultMessage)
            ->never();

        // Execute
        $sut->formatElementMessage($element, "Value is required and can't be empty", $messageKey);
    }

    /**
     * @return array[]
     */
    public function defaultMessageKeyMessagesProvider(): array
    {
        return [
            'NotEmpty::IS_EMPTY default message' => [NotEmpty::IS_EMPTY, "Value is required and can't be empty"],
        ];
    }

    /**
     * @test
     * @depends formatElementMessage_IsCallable
     * @dataProvider defaultMessageKeyMessagesProvider
     * @param string $messageKey
     * @param string $defaultMessage
     */
    public function formatElementMessage_ReplacesDefaultMessage(string $messageKey, string $defaultMessage)
    {
        // Setup
        $serviceLocator = $this->setUpServiceLocator();
        $sut = $this->setUpSut($serviceLocator);
        $element = $this->setUpElement();
        $element->setAttribute('type', $expectedType = 'foo');
        $replacementMessageKey = sprintf('validation.element.%s.%s', $expectedType, NotEmpty::IS_EMPTY);
        $this->resolveMockService($serviceLocator, TranslatorInterface::class)
            ->shouldReceive('translate')
            ->with($replacementMessageKey)
            ->andReturn($replacementMessage = 'bar');

        // Execute
        $result = $sut->formatElementMessage($element, $defaultMessage, $messageKey);

        // Assert
        $this->assertStringContainsString($replacementMessage, strtolower($result));
    }

    /**
     * @test
     * @todo depends render_ReplacesDefaultMessage
     * @dataProvider defaultMessageKeyMessagesProvider
     */
    public function formatElementMessage_ReplacesDefaultMessage_WhenDefaultMessageIsTranslated(string $messageKey, string $defaultMessage)
    {
        // Setup
        $serviceLocator = $this->setUpServiceLocator();
        $sut = $this->setUpSut($serviceLocator);
        $element = $this->setUpElement();
        $element->setAttribute('type', $expectedType = 'foo');
        $translatedMessage = 'bar';
        $replacementMessageKey = sprintf('validation.element.%s.%s', $expectedType, $messageKey);
        $this->resolveMockService($serviceLocator, TranslatorInterface::class)
            ->shouldReceive('translate')
            ->with($replacementMessageKey)
            ->andReturn($replacementMessage = 'replacement-message');
        $this->resolveMockService($serviceLocator, TranslatorInterface::class)
            ->shouldReceive('translate')
            ->with($defaultMessage)
            ->andReturn($translatedMessage);

        // Execute
        $result = $sut->formatElementMessage($element, $translatedMessage, $messageKey);

        // Assert
        $this->assertStringContainsString($replacementMessage, strtolower($result));
    }

    /**
     * @test
     * @depends formatElementMessage_ReplacesDefaultMessage
     */
    public function formatElementMessage_UsesOriginalMessage_WhenCustomValidationMessageUsed()
    {
        // Setup
        $serviceLocator = $this->setUpServiceLocator();
        $sut = $this->setUpSut($serviceLocator);
        $element = $this->setUpElement();
        $element->setAttribute('type', $type = 'foo');
        $messageKey = NotEmpty::IS_EMPTY;
        $expectedMessage = 'foo bar baz';

        // Define Expectations
        $this->resolveMockService($serviceLocator, TranslatorInterface::class)
            ->shouldReceive('translate')
            ->andReturnUsing(function ($key) use ($type, $messageKey) {
                $prefix = sprintf('validation.element.%s.%s', $type, $messageKey);
                return substr($key, 0, strlen($prefix)) === $prefix ? 'default' : $key;
            });

        // Execute
        $result = $sut->formatElementMessage($element, $expectedMessage, $messageKey);

        // Assert
        $this->assertStringContainsString($expectedMessage, strtolower($result));
    }

    /**
     * @test
     * @depends formatElementMessage_ReplacesDefaultMessage
     */
    public function formatElementMessage_UsesOriginalMessage_WhenMessageIsCustom_AndTranslatesOriginalMessage()
    {
        // Setup
        $serviceLocator = $this->setUpServiceLocator();
        $sut = $this->setUpSut($serviceLocator);
        $element = $this->setUpElement();
        $element->setAttribute('type', 'foo');
        $this->resolveMockService($serviceLocator, TranslatorInterface::class)
            ->shouldReceive('translate')
            ->with('foo bar baz')
            ->andReturn($expectedMessage = 'bip');

        // Execute
        $result = $sut->formatElementMessage($element, 'foo bar baz', NotEmpty::IS_EMPTY);

        // Assert
        $this->assertStringContainsString($expectedMessage, strtolower($result));
    }

    /**
     * @test
     * @depends formatElementMessage_ReplacesDefaultMessage
     */
    public function formatElementMessage_UsesOriginalMessage_WhenMessageIsCustom_AndDoesNotTranslateOriginalMessage_IfDisabledUsingMessageInterface()
    {
        // Setup
        $serviceLocator = $this->setUpServiceLocator();
        $sut = $this->setUpSut($serviceLocator);
        $element = $this->setUpElement();
        $message = new GenericValidationMessage();
        $message->setMessage($originalMessageText = 'foo bar baz');
        $message->setShouldTranslate(false);
        $this->resolveMockService($serviceLocator, TranslatorInterface::class)
            ->shouldReceive('translate')
            ->with($originalMessageText)
            ->andReturn('bip');

        // Execute
        $result = $sut->formatElementMessage($element, $message, NotEmpty::IS_EMPTY);

        // Assert
        $this->assertStringContainsString($originalMessageText, strtolower($result));
    }

    /**
     * @test
     * @depends formatElementMessage_ReplacesDefaultMessage
     */
    public function formatElementMessage_UsesOriginalMessage_WhenNoValidatorIsMappedToTheMessageKey()
    {
        // Setup
        $serviceLocator = $this->setUpServiceLocator();
        $sut = $this->setUpSut($serviceLocator);
        $element = $this->setUpElement();
        $element->setAttribute('type', 'foo');

        // Execute
        $result = $sut->formatElementMessage($element, $expectedMessage = 'foo bar baz', 'baz bar foo');

        // Assert
        $this->assertStringContainsString($expectedMessage, strtolower($result));
    }

    /**
     * @test
     * @depends formatElementMessage_IsCallable
     */
    public function formatElementMessage_UsesOriginalMessage_IfNoTranslationIsAvailable()
    {
        // Setup
        $serviceLocator = $this->setUpServiceLocator();
        $sut = $this->setUpSut($serviceLocator);
        $element = $this->setUpElement();
        $element->setAttribute('type', 'foo');

        // Execute
        $result = $sut->formatElementMessage($element, $expectedMessage = 'foo bar baz', NotEmpty::IS_EMPTY);

        // Assert
        $this->assertStringContainsString($expectedMessage, strtolower($result));
    }

    /**
     * @test
     * @depends formatElementMessage_IsCallable
     */
    public function formatElementMessage_UsesOriginalMessage_IfFieldLabelIsEmpty()
    {
        // Setup
        $serviceLocator = $this->setUpServiceLocator();
        $sut = $this->setUpSut($serviceLocator);
        $element = $this->setUpElement();
        $element->setAttribute('type', $type = 'foo');
        $element->setLabel('');
        $messageKey = NotEmpty::IS_EMPTY;
        $this->resolveMockService($serviceLocator, TranslatorInterface::class)
            ->shouldReceive('translate')
            ->with(sprintf('validation.element.%s.%s', $type, $messageKey))
            ->andReturn(FormElementMessageFormatter::FIELD_LABEL_PLACEHOLDER);

        // Execute
        $result = $sut->formatElementMessage($element, "Value is required and can't be empty", $messageKey);

        // Assert
        $this->assertRegExp("/Value is required and can(\&\#039\;|\')t be empty/", $result);
    }

    /**
     * @test
     * @depends formatElementMessage_IsCallable
     */
    public function formatElementMessage_UsesDefaultMessageForElementType_IfElementTypeIsNotSet()
    {
        // Setup
        $serviceLocator = $this->setUpServiceLocator();
        $sut = $this->setUpSut($serviceLocator);
        $element = $this->setUpElement();
        $this->resolveMockService($serviceLocator, TranslatorInterface::class)
            ->shouldReceive('translate')
            ->with('validation.element.default.' . NotEmpty::IS_EMPTY)
            ->andReturn('foo')
            ->twice();

        // Execute
        $result = $sut->formatElementMessage($element, "Value is required and can't be empty", NotEmpty::IS_EMPTY);

        // Assert
        $this->assertStringContainsString('foo', strtolower($result));
    }

    /**
     * @test
     * @depends formatElementMessage_IsCallable
     */
    public function formatElementMessage_UsesDefaultMessageForElementType_IfElementTypeHasNoTranslation()
    {
        // Setup
        $serviceLocator = $this->setUpServiceLocator();
        $sut = $this->setUpSut($serviceLocator);
        $element = $this->setUpElement();
        $element->setAttribute('type', 'password');
        $this->resolveMockService($serviceLocator, TranslatorInterface::class)
            ->shouldReceive('translate')
            ->with('validation.element.default.' . NotEmpty::IS_EMPTY)
            ->andReturn($expectedMessage = 'foo')
            ->twice();

        // Execute
        $result = $sut->formatElementMessage($element, "Value is required and can't be empty", NotEmpty::IS_EMPTY);

        // Assert
        $this->assertStringContainsString($expectedMessage, strtolower($result));
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
     * @return FormElementMessageFormatter
     */
    protected function setUpSut(ServiceLocatorInterface $serviceLocator): FormElementMessageFormatter
    {
        return (new FormElementMessageFormatterFactory())->createService($serviceLocator);
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
