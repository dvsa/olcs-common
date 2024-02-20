<?php

declare(strict_types=1);

namespace CommonTest\Form\Elements\Validators\Messages;

use Common\Form\Elements\Validators\Messages\FormElementMessageFormatter;
use Common\Form\Elements\Validators\Messages\FormElementMessageFormatterFactory;
use Common\Form\Elements\Validators\Messages\GenericValidationMessage;
use Common\Test\Form\Element\ElementBuilder;
use Common\Test\MockeryTestCase;
use Common\Test\MocksServicesTrait;
use Common\Test\Translator\MocksTranslatorsTrait;
use Hamcrest\Matcher;
use Hamcrest\Text\MatchesPattern;
use Psr\Container\ContainerInterface;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\ServiceManager\ServiceManager;
use Laminas\Validator\ValidatorPluginManager;

/**
 * @see FormElementMessageFormatter
 */
class FormElementMessageFormatterTest extends MockeryTestCase
{
    use MocksServicesTrait;
    use MocksTranslatorsTrait;

    protected const VALIDATOR_MANAGER = 'ValidatorManager';
    protected const ELEM_TYPE = 'ELEMENT TYPE';
    protected const ELEM_TYPE_WITH_NO_TRANSLATION = 'ELEMENT TYPE WITH NO TRANSLATION';
    protected const MISSING_ELEM_TYPE_REPLACEMENT = 'default';
    protected const LABEL_PLACEHOLDER = '{{fieldLabel}}';
    protected const LABEL_WITH_HTML = '<strong>LABEL WITH HTML</strong>';
    protected const LABEL_WITH_NO_CONTENT = '';
    protected const LABEL = 'LABEL WITH CONTENT';
    protected const LABEL_WITH_TRAILING_WHITESPACE = 'LABEL WITH TRAILING WHITESPACE    ';
    protected const REPLACEMENT_MESSAGE_WITH_LABEL_PLACEHOLDER = 'REPLACEMENT MESSAGE WITH FIELD LABEL: "{{fieldLabel}}"';
    protected const REPLACEMENT_MESSAGE_WITHOUT_PLACEHOLDER = 'REPLACEMENT MESSAGE WITHOUT PLACEHOLDER';
    protected const MESSAGE_KEY = 'MESSAGE KEY';
    protected const DEFAULT_MESSAGE = 'DEFAULT MESSAGE';
    protected const DEFAULT_MESSAGE_TRANSLATED = 'DEFAULT MESSAGE TRANSLATED';
    protected const MESSAGE_WITHOUT_PLACEHOLDER = 'MESSAGE WITHOUT PLACEHOLDER';
    protected const MESSAGE_WITHOUT_PLACEHOLDER_TRANSLATED = 'MESSAGE WITHOUT PLACEHOLDER TRANSLATED';
    protected const MESSAGE_WITH_LABEL_PLACEHOLDER = 'CUSTOM MESSAGE WITH FIELD LABEL: "{{fieldLabel}}"';
    protected const MESSAGE_WITH_LABEL_PLACEHOLDER_REPLACED_WITH_EMPTY_LABEL = 'CUSTOM MESSAGE WITH FIELD LABEL: ""';
    protected const MESSAGE_WITH_LABEL_PLACEHOLDER_REPLACED_WITH_NON_EMPTY_LABEL = 'CUSTOM MESSAGE WITH FIELD LABEL: "LABEL WITH CONTENT"';
    protected const MESSAGE_WITH_LABEL_PLACEHOLDER_REPLACED_WITH_TRIMMED_LABEL_WITH_TRAILING_WHITESPACE = 'CUSTOM MESSAGE WITH FIELD LABEL: "LABEL WITH TRAILING WHITESPACE"';
    protected const DEFAULT_REPLACEMENT_WHERE_ELEMENT_TYPE_DOES_NOT_HAVE_ITS_OWN_TRANSLATION = 'validation.element.default.MESSAGE KEY';
    protected const SHORT_LABEL = 'SHORT LABEL';
    protected const FORMATTED_SHORT_LABEL_WITH_DEFAULT_MESSAGE = 'SHORT LABEL: DEFAULT MESSAGE';
    protected const UNTRANSLATED_MESSAGE = 'UNTRANSLATED MESSAGE';
    protected const TRANSLATED_MESSAGE = 'TRANSLATED MESSAGE';
    protected const FORMATTED_SHORT_LABEL_WITH_TRANSLATED_MESSAGE = 'SHORT LABEL: TRANSLATED MESSAGE';

    /**
     * @var FormElementMessageFormatter|null
     */
    protected $sut;

    /**
     * @test
     */
    public function getReplacementFor_IsCallable()
    {
        // Setup
        $this->sut = $this->setUpSut($this->serviceManager());

        // Assert
        $this->assertIsCallable([$this->sut, 'getReplacementFor']);
    }

    /**
     * @test
     */
    public function enableReplacementOfMessage_IsCallable()
    {
        // Setup
        $this->sut = $this->setUpSut($this->serviceManager());

        // Assert
        $this->assertIsCallable([$this->sut, 'enableReplacementOfMessage']);
    }

    /**
     * @test
     * @depends enableReplacementOfMessage_IsCallable
     */
    public function enableReplacementOfMessage_SetsDefaultMessageProviderForMessagesWithKey()
    {
        // Setup
        $this->sut = $this->setUpSut($this->serviceManager());
        $defaultMessageProvider = function ($val) {
            return $val;
        };

        // Execute
        $this->sut->enableReplacementOfMessage(static::MESSAGE_KEY, $defaultMessageProvider);

        // Assert
        $this->assertSame($defaultMessageProvider, $this->sut->getReplacementFor(static::MESSAGE_KEY));
    }

    /**
     * @test
     * @depends enableReplacementOfMessage_IsCallable
     */
    public function enableReplacementOfMessage_EncapsulatesTextReplacements_IsCallable()
    {
        // Setup
        $this->sut = $this->setUpSut($this->serviceManager());

        // Execute
        $this->sut->enableReplacementOfMessage(static::MESSAGE_KEY, static::DEFAULT_MESSAGE);

        // Assert
        $provider = $this->sut->getReplacementFor(static::MESSAGE_KEY);
        $this->assertIsCallable($provider);
    }

    /**
     * @test
     * @depends enableReplacementOfMessage_EncapsulatesTextReplacements_IsCallable
     */
    public function enableReplacementOfMessage_EncapsulatesTextReplacements_IsCallableThatReturnsOriginalText()
    {
        // Setup
        $this->sut = $this->setUpSut($this->serviceManager());

        // Execute
        $this->sut->enableReplacementOfMessage(static::MESSAGE_KEY, static::DEFAULT_MESSAGE);

        // Assert
        $provider = $this->sut->getReplacementFor(static::MESSAGE_KEY);
        $this->assertEquals(static::DEFAULT_MESSAGE, call_user_func($provider));
    }

    /**
     * @test
     */
    public function formatElementMessage_IsCallable()
    {
        // Setup
        $this->sut = $this->setUpSut($this->serviceManager());

        // Assert
        $this->assertIsCallable([$this->sut, 'formatElementMessage']);
    }

    /**
     * @test
     * @depends formatElementMessage_IsCallable
     */
    public function formatElementMessage_ReturnsString()
    {
        // Setup
        $this->sut = $this->setUpSut($this->serviceManager());
        $element = ElementBuilder::anElement()->build();

        // Execute
        $formattedMessage = $this->sut->formatElementMessage($element, static::DEFAULT_MESSAGE, static::MESSAGE_KEY);

        // Assert
        $this->assertIsString($formattedMessage);
    }

    /**
     * @test
     * @depends formatElementMessage_ReturnsString
     */
    public function formatElementMessage_AcceptsNullElementLabels()
    {
        // Setup
        $this->sut = $this->setUpSut($this->serviceManager());
        $element = ElementBuilder::anElement()->build();

        // Execute
        $formattedMessage = $this->sut->formatElementMessage($element, static::DEFAULT_MESSAGE, static::MESSAGE_KEY);

        // Assert
        $this->assertIsString($formattedMessage);
    }

    /**
     * @test
     * @depends formatElementMessage_IsCallable
     */
    public function formatElementMessage_ReplacesFieldLabelPlaceholder_InCustomMessage()
    {
        // Setup
        $this->sut = $this->setUpSut($this->serviceManager());
        $element = ElementBuilder::anElement()->withLabel(static::LABEL)->build();

        // Execute
        $formattedMessage = $this->sut->formatElementMessage($element, static::MESSAGE_WITH_LABEL_PLACEHOLDER, static::MESSAGE_KEY);

        // Assert
        $this->assertEquals(static::MESSAGE_WITH_LABEL_PLACEHOLDER_REPLACED_WITH_NON_EMPTY_LABEL, $formattedMessage);
    }

    /**
     * @test
     * @depends formatElementMessage_ReplacesFieldLabelPlaceholder_InCustomMessage
     */
    public function formatElementMessage_ReplacesFieldLabelPlaceholder_InCustomMessage_WithEmptyString_WhenLabelEmpty()
    {
        // Setup
        $this->sut = $this->setUpSut($this->serviceManager());
        $element = ElementBuilder::anElement()->withLabel(static::LABEL_WITH_NO_CONTENT)->build();

        // Execute
        $formattedMessage = $this->sut->formatElementMessage($element, static::MESSAGE_WITH_LABEL_PLACEHOLDER, static::MESSAGE_KEY);

        // Assert
        $this->assertEquals(static::MESSAGE_WITH_LABEL_PLACEHOLDER_REPLACED_WITH_EMPTY_LABEL, $formattedMessage);
    }

    /**
     * @test
     * @depends formatElementMessage_ReplacesFieldLabelPlaceholder_InCustomMessage
     */
    public function formatElementMessage_ReplacesFieldLabelPlaceholder_InCustomMessage_AsTrimmed()
    {
        //setup
        $serviceLocator = $this->setUpServiceManager();
        $this->sut = $this->setUpSut($serviceLocator);
        $element = ElementBuilder::anElement()->withLabel(static::LABEL_WITH_TRAILING_WHITESPACE)->build();

        //Execute
        $formattedMessage = $this->sut->formatElementMessage($element, static::MESSAGE_WITH_LABEL_PLACEHOLDER, static::MESSAGE_KEY);

        //Assert
        $this->assertEquals(static::MESSAGE_WITH_LABEL_PLACEHOLDER_REPLACED_WITH_TRIMMED_LABEL_WITH_TRAILING_WHITESPACE, $formattedMessage);
    }

    /**
     * @test
     * @testdox Replaces the field label placeholder with a label that is only translated once. It is important that the
     * replacement message is not translated a second time before having any variables replaced. This is because, at the
     * time of writing this, there is an issue with the MissingTranslationProcessor which will change the placeholder
     * prefix/suffix curly braces so that they no longer get correctly replaced.
     * @depends formatElementMessage_ReplacesFieldLabelPlaceholder_InCustomMessage
     */
    public function formatElementMessage_ReplacesVariablesBeforeTranslating()
    {
        // Setup
        $this->sut = $this->setUpSut($this->serviceManager());
        $element = ElementBuilder::anElement()->withLabel(static::LABEL)->build();
        $this->enableReplacementOfMessage(static::MESSAGE_KEY, static::DEFAULT_MESSAGE)->andReturn(static::REPLACEMENT_MESSAGE_WITHOUT_PLACEHOLDER);

        // Execute
        $this->sut->formatElementMessage($element, static::DEFAULT_MESSAGE, static::MESSAGE_KEY);

        // Assert
        $this->translator()->shouldNotHaveReceived('translate', [static::REPLACEMENT_MESSAGE_WITHOUT_PLACEHOLDER]);
    }

    /**
     * @test
     * @depends formatElementMessage_IsCallable
     */
    public function formatElementMessage_ReplacesDefaultMessage_WhenElementTypeIsSet()
    {
        // Setup
        $this->sut = $this->setUpSut($this->serviceManager());
        $element = ElementBuilder::anElement()->withType(static::ELEM_TYPE)->build();
        $this->enableReplacementOfMessage(static::MESSAGE_KEY, static::DEFAULT_MESSAGE)->andReturn(static::REPLACEMENT_MESSAGE_WITHOUT_PLACEHOLDER);

        // Execute
        $formattedMessage = $this->sut->formatElementMessage($element, static::DEFAULT_MESSAGE, static::MESSAGE_KEY);

        // Assert
        $this->assertEquals(static::REPLACEMENT_MESSAGE_WITHOUT_PLACEHOLDER, $formattedMessage);
    }

    /**
     * @test
     * @depends formatElementMessage_ReplacesDefaultMessage_WhenElementTypeIsSet
     */
    public function formatElementMessage_ReplacesDefaultMessage_WhenDefaultMessageIsTranslated()
    {
        // Setup
        $this->sut = $this->setUpSut($this->serviceManager());
        $element = ElementBuilder::anElement()->withType(static::ELEM_TYPE)->build();
        $this->enableReplacementOfMessage(static::MESSAGE_KEY, static::DEFAULT_MESSAGE)->andReturn(static::REPLACEMENT_MESSAGE_WITHOUT_PLACEHOLDER);
        $this->translator()->shouldReceive('translate')->with(static::DEFAULT_MESSAGE)->andReturn(static::DEFAULT_MESSAGE_TRANSLATED);

        // Execute
        $formattedMessage = $this->sut->formatElementMessage($element, static::DEFAULT_MESSAGE_TRANSLATED, static::MESSAGE_KEY);

        // Assert
        $this->assertEquals(static::REPLACEMENT_MESSAGE_WITHOUT_PLACEHOLDER, $formattedMessage);
    }

    /**
     * @test
     * @depends formatElementMessage_ReplacesDefaultMessage_WhenElementTypeIsSet
     */
    public function formatElementMessage_ReplacesDefaultMessage_IfElementTypeIsNotSet()
    {
        // Setup
        $this->sut = $this->setUpSut($this->serviceManager());
        $element = ElementBuilder::anElement()->build();
        $this->sut->enableReplacementOfMessage(static::MESSAGE_KEY, static::DEFAULT_MESSAGE);
        $this->translator()
            ->shouldReceive('translate')
            ->with($this->replacementMessageMatching(static::MESSAGE_KEY, static::MISSING_ELEM_TYPE_REPLACEMENT))
            ->andReturn(static::REPLACEMENT_MESSAGE_WITHOUT_PLACEHOLDER);

        // Execute
        $formattedMessage = $this->sut->formatElementMessage($element, static::DEFAULT_MESSAGE, static::MESSAGE_KEY);

        // Assert
        $this->assertStringContainsString(static::REPLACEMENT_MESSAGE_WITHOUT_PLACEHOLDER, $formattedMessage);
    }

    /**
     * @test
     * @depends formatElementMessage_ReplacesDefaultMessage_WhenElementTypeIsSet
     */
    public function formatElementMessage_ReplacesDefaultMessage_IfElementTypeHasNoTranslation()
    {
        // Setup
        $this->sut = $this->setUpSut($this->serviceManager());
        $element = ElementBuilder::anElement()->withType(static::ELEM_TYPE_WITH_NO_TRANSLATION)->build();
        $this->sut->enableReplacementOfMessage(static::MESSAGE_KEY, static::DEFAULT_MESSAGE);
        $this->translator()
            ->shouldReceive('translate')
            ->with(static::DEFAULT_REPLACEMENT_WHERE_ELEMENT_TYPE_DOES_NOT_HAVE_ITS_OWN_TRANSLATION)
            ->andReturn(static::REPLACEMENT_MESSAGE_WITHOUT_PLACEHOLDER);

        // Execute
        $formattedMessage = $this->sut->formatElementMessage($element, static::DEFAULT_MESSAGE, static::MESSAGE_KEY);

        // Assert
        $this->assertEquals(static::REPLACEMENT_MESSAGE_WITHOUT_PLACEHOLDER, $formattedMessage);
    }

    /**
     * @test
     * @depends formatElementMessage_ReplacesDefaultMessage_IfElementTypeIsNotSet
     */
    public function formatElementMessage_UsesOriginalMessage_WhenCustomValidationMessageUsed()
    {
        // Setup
        $this->sut = $this->setUpSut($this->serviceManager());
        $element = ElementBuilder::anElement()->build();
        $this->enableReplacementOfMessage(static::MESSAGE_KEY, static::DEFAULT_MESSAGE);

        // Execute
        $formattedMessage = $this->sut->formatElementMessage($element, static::MESSAGE_WITHOUT_PLACEHOLDER, static::MESSAGE_KEY);

        // Assert
        $this->assertEquals(static::MESSAGE_WITHOUT_PLACEHOLDER, $formattedMessage);
    }

    /**
     * @test
     * @depends formatElementMessage_UsesOriginalMessage_WhenCustomValidationMessageUsed
     */
    public function formatElementMessage_TranslatesCustomMessages()
    {
        // Setup
        $this->sut = $this->setUpSut($this->serviceManager());
        $element = ElementBuilder::anElement()->build();
        $this->resolveMockService($this->serviceManager(), TranslatorInterface::class)
            ->shouldReceive('translate')
            ->with(static::MESSAGE_WITHOUT_PLACEHOLDER)
            ->andReturn(static::MESSAGE_WITHOUT_PLACEHOLDER_TRANSLATED);

        // Execute
        $formattedMessage = $this->sut->formatElementMessage($element, static::MESSAGE_WITHOUT_PLACEHOLDER, static::MESSAGE_KEY);

        // Assert
        $this->assertEquals(static::MESSAGE_WITHOUT_PLACEHOLDER_TRANSLATED, $formattedMessage);
    }

    /**
     * @test
     * @depends formatElementMessage_TranslatesCustomMessages
     */
    public function formatElementMessage_DoesNotTranslateCustomMessages_IfTranslationDisabledUsingMessageInterface()
    {
        // Setup
        $this->sut = $this->setUpSut($this->serviceManager());
        $element = ElementBuilder::anElement()->build();
        $message = new GenericValidationMessage();
        $message->setMessage(static::MESSAGE_WITHOUT_PLACEHOLDER);
        $message->setShouldTranslate(false);
        $this->resolveMockService($this->serviceManager(), TranslatorInterface::class)
            ->shouldReceive('translate')
            ->with(static::MESSAGE_WITHOUT_PLACEHOLDER)
            ->andReturn(static::MESSAGE_WITHOUT_PLACEHOLDER_TRANSLATED);

        // Execute
        $formattedMessage = $this->sut->formatElementMessage($element, $message, static::MESSAGE_KEY);

        // Assert
        $this->assertEquals(static::MESSAGE_WITHOUT_PLACEHOLDER, $formattedMessage);
    }

    /**
     * @test
     * @depends formatElementMessage_ReplacesDefaultMessage_IfElementTypeIsNotSet
     */
    public function formatElementMessage_UsesOriginalMessage_WhenReplacementIsNotEnabledForAMessageKey()
    {
        // Setup
        $this->sut = $this->setUpSut($this->serviceManager());
        $element = ElementBuilder::anElement()->build();

        // Execute
        $formattedMessage = $this->sut->formatElementMessage($element, static::MESSAGE_WITHOUT_PLACEHOLDER, static::MESSAGE_KEY);

        // Assert
        $this->assertEquals(static::MESSAGE_WITHOUT_PLACEHOLDER, $formattedMessage);
    }

    /**
     * @test
     * @depends formatElementMessage_ReplacesDefaultMessage_IfElementTypeIsNotSet
     */
    public function formatElementMessage_UsesOriginalMessage_WhenReplacementEnabledForMessage_ButNoTranslationIsAvailable()
    {
        // Setup
        $this->sut = $this->setUpSut($this->serviceManager());
        $element = ElementBuilder::anElement()->build();
        $this->sut->enableReplacementOfMessage(static::MESSAGE_KEY, static::DEFAULT_MESSAGE);

        // Execute
        $formattedMessage = $this->sut->formatElementMessage($element, static::DEFAULT_MESSAGE, static::MESSAGE_KEY);

        // Assert
        $this->assertEquals(static::DEFAULT_MESSAGE, $formattedMessage);
    }

    /**
     * @test
     * @depends formatElementMessage_ReplacesDefaultMessage_IfElementTypeIsNotSet
     */
    public function formatElementMessage_DoesNotUseReplacementMessage_ContainingLabelPlaceholder_IfElementLabelIsEmpty()
    {
        // Setup
        $this->sut = $this->setUpSut($this->serviceManager());
        $this->enableReplacementOfMessage(static::MESSAGE_KEY, static::DEFAULT_MESSAGE)->andReturn(static::REPLACEMENT_MESSAGE_WITH_LABEL_PLACEHOLDER);
        $element = ElementBuilder::anElement()->withLabel(static::LABEL_WITH_NO_CONTENT)->build();

        // Execute
        $formattedMessage = $this->sut->formatElementMessage($element, static::DEFAULT_MESSAGE, static::MESSAGE_KEY);

        // Assert
        $this->assertEquals(static::DEFAULT_MESSAGE, $formattedMessage);
    }

    /**
     * @test
     * @depends formatElementMessage_ReplacesDefaultMessage_IfElementTypeIsNotSet
     */
    public function formatElementMessage_DoesNotUseReplacementMessage_ContainingLabelPlaceholder_IfElementLabelContainsHtml()
    {
        // Setup
        $this->sut = $this->setUpSut($this->serviceManager());
        $this->enableReplacementOfMessage(static::MESSAGE_KEY, static::DEFAULT_MESSAGE)->andReturn(static::REPLACEMENT_MESSAGE_WITH_LABEL_PLACEHOLDER);
        $element = ElementBuilder::anElement()->withLabel(static::LABEL_WITH_HTML)->build();

        // Execute
        $formattedMessage = $this->sut->formatElementMessage($element, static::DEFAULT_MESSAGE, static::MESSAGE_KEY);

        // Assert
        $this->assertEquals(static::DEFAULT_MESSAGE, $formattedMessage);
    }

    /**
     * @test
     * @depends formatElementMessage_DoesNotUseReplacementMessage_ContainingLabelPlaceholder_IfElementLabelContainsHtml
     */
    public function formatElementMessage_DoesNotUseReplacementMessage_ContainingLabelPlaceholder_IfElementLabelContainsHtml_AfterBeingTranslated()
    {
        // Setup
        $this->sut = $this->setUpSut($this->serviceManager());
        $this->enableReplacementOfMessage(static::MESSAGE_KEY, static::DEFAULT_MESSAGE)->andReturn(static::REPLACEMENT_MESSAGE_WITH_LABEL_PLACEHOLDER);
        $element = ElementBuilder::anElement()->withLabel(static::LABEL)->build();
        $this->translator()->shouldReceive('translate')->with(static::LABEL)->andReturn(static::LABEL_WITH_HTML);

        // Execute
        $formattedMessage = $this->sut->formatElementMessage($element, static::DEFAULT_MESSAGE, static::MESSAGE_KEY);

        // Assert
        $this->assertEquals(static::DEFAULT_MESSAGE, $formattedMessage);
    }

    /**
     * @test
     * @depends formatElementMessage_ReturnsString
     */
    public function formatElementMessage_ReturnsShortLabel()
    {
        // Setup
        $this->sut = $this->setUpSut($this->serviceManager());
        $element = ElementBuilder::anElement()->withShortLabel(static::SHORT_LABEL)->build();

        // Execute
        $formattedMessage = $this->sut->formatElementMessage($element, static::DEFAULT_MESSAGE, static::MESSAGE_KEY);

        // Assert
        $this->assertEquals(static::FORMATTED_SHORT_LABEL_WITH_DEFAULT_MESSAGE, $formattedMessage);
    }

    /**
     * @test
     * @depends formatElementMessage_ReturnsShortLabel
     */
    public function formatElementMessage_ReturnsShortLabel_WithTranslatedMessage()
    {
        // Setup
        $this->sut = $this->setUpSut($this->serviceManager());
        $element = ElementBuilder::anElement()->withShortLabel(static::SHORT_LABEL)->build();
        $this->translator()->shouldReceive('translate')->with(static::UNTRANSLATED_MESSAGE)->andReturn(static::TRANSLATED_MESSAGE);

        // Execute
        $formattedMessage = $this->sut->formatElementMessage($element, static::UNTRANSLATED_MESSAGE, static::MESSAGE_KEY);

        // Assert
        $this->assertEquals(static::FORMATTED_SHORT_LABEL_WITH_TRANSLATED_MESSAGE, $formattedMessage);
    }

    /**
     * @param string $messageKey
     * @param string $messageDefault
     * @return object
     */
    protected function enableReplacementOfMessage(string $messageKey, string $messageDefault): object
    {
        $this->sut->enableReplacementOfMessage($messageKey, $messageDefault);
        return $this->translator()
            ->shouldReceive('translate')
            ->with($this->replacementMessageMatching($messageKey))
            ->andReturn(static::REPLACEMENT_MESSAGE_WITHOUT_PLACEHOLDER);
    }

    /**
     * Gets a matcher that matches any untranslated replacement message for a given message key.
     *
     * @param string $messageKey
     * @param string|null $type
     * @return Matcher
     */
    protected function replacementMessageMatching(string $messageKey, string $type = null): Matcher
    {
        if (null === $type) {
            $type = '.+';
        }
        return MatchesPattern::matchesPattern(sprintf('/validation\.element\.%s\.%s/', $type, $messageKey));
    }

    protected function setUp(): void
    {
        $this->setUpServiceManager();
    }

    protected function setUpSut(ContainerInterface $serviceLocator): FormElementMessageFormatter
    {
        return (new FormElementMessageFormatterFactory())->__invoke($serviceLocator, FormElementMessageFormatter::class);
    }

    /**
     * @param ServiceManager $serviceManager
     */
    protected function setUpDefaultServices(ServiceManager $serviceManager)
    {
        $serviceManager->setService(TranslatorInterface::class, $this->setUpDefaultTranslator());
        $serviceManager->setService(static::VALIDATOR_MANAGER, new ValidatorPluginManager());
        $serviceManager->setFactory(FormElementMessageFormatter::class, new FormElementMessageFormatterFactory());
    }
}
