<?php

declare(strict_types=1);

namespace CommonTest\Form\Elements\Validators\Messages;

use Common\Form\Elements\Validators\Messages\FormElementMessageFormatter;
use Common\Form\Elements\Validators\Messages\FormElementMessageFormatterFactory;
use Common\Form\Elements\Validators\Messages\ValidatorDefaultMessageProvider;
use Common\Test\MockeryTestCase;
use Common\Test\MocksServicesTrait;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\ServiceManager\ServiceManager;
use Laminas\Validator\ValidatorPluginManager;

/**
 * @see FormElementMessageFormatterFactory
 */
class FormElementMessageFormatterFactoryTest extends MockeryTestCase
{
    use MocksServicesTrait;

    protected const VALIDATOR_MANAGER = 'ValidatorManager';
    protected const MESSAGE_KEY = 'MESSAGE KEY';
    protected const VALIDATOR_NAME = 'VALIDATOR NAME';
    protected const CONFIG_SERVICE = 'config';
    protected const VALIDATION_CONFIG_NAMESPACE = 'validation';
    protected const DEFAULT_MESSAGE_TEMPLATES_TO_REPLACE_VARIABLE = 'default_message_templates_to_replace';

    /**
     * @var FormElementMessageFormatterFactory
     */
    protected $sut;

    /**
     * @test
     */
    public function __invoke_IsCallable()
    {
        // Setup
        $this->sut = $this->setUpSut();

        // Assert
        $this->assertIsCallable([$this->sut, '__invoke']);
    }

    /**
     * @test
     * @depends __invoke_IsCallable
     */
    public function __invoke_ReturnsInstanceOfFormElementMessageFormatter()
    {
        // Setup
        $this->sut = $this->setUpSut();

        // Execute
        $formatter = $this->sut->__invoke($this->serviceManager(), FormElementMessageFormatter::class);

        // Assert
        $this->assertInstanceOf(FormElementMessageFormatter::class, $formatter);
    }

    /**
     * @test
     * @depends __invoke_ReturnsInstanceOfFormElementMessageFormatter
     */
    public function __invoke_RegistersReplacementOfMessageKey_WithProvider_ThatIsInstanceOfValidatorDefaultMessageProvider()
    {
        // Setup
        $this->sut = $this->setUpSut();
        $this->registerDefaultMessageTemplateToReplace(static::MESSAGE_KEY, static::VALIDATOR_NAME);

        // Execute
        $formatter = $this->sut->__invoke($this->serviceManager(), FormElementMessageFormatter::class);

        // Assert
        $replacement = $formatter->getReplacementFor(static::MESSAGE_KEY);
        $this->assertInstanceOf(ValidatorDefaultMessageProvider::class, $replacement);
    }

    /**
     * @test
     * @depends __invoke_RegistersReplacementOfMessageKey_WithProvider_ThatIsInstanceOfValidatorDefaultMessageProvider
     */
    public function __invoke_RegistersReplacementOfMessageKey_WithProvider_ForValidatorInConfig()
    {
        // Setup
        $this->sut = $this->setUpSut();
        $this->registerDefaultMessageTemplateToReplace(static::MESSAGE_KEY, static::VALIDATOR_NAME);

        // Execute
        $formatter = $this->sut->__invoke($this->serviceManager(), FormElementMessageFormatter::class);

        // Assert
        $replacement = $formatter->getReplacementFor(static::MESSAGE_KEY);
        $this->assertEquals(static::VALIDATOR_NAME, $replacement->getValidatorName());
    }

    protected function setUp(): void
    {
        $this->setUpServiceManager();
    }

    /**
     * @return FormElementMessageFormatterFactory
     */
    protected function setUpSut(): FormElementMessageFormatterFactory
    {
        return new FormElementMessageFormatterFactory();
    }

    /**
     * @param ServiceManager $serviceManager
     */
    protected function setUpDefaultServices(ServiceManager $serviceManager)
    {
        $serviceManager->setService(TranslatorInterface::class, $this->setUpMockService(TranslatorInterface::class));
        $serviceManager->setService(static::CONFIG_SERVICE, [
            static::VALIDATION_CONFIG_NAMESPACE => [static::DEFAULT_MESSAGE_TEMPLATES_TO_REPLACE_VARIABLE => []],
        ]);
        $serviceManager->setService(static::VALIDATOR_MANAGER, new ValidatorPluginManager());
    }

    /**
     * @param string $messageKey
     * @param string $validatorClassReference
     */
    protected function registerDefaultMessageTemplateToReplace(string $messageKey, string $validatorClassReference)
    {
        $config = $this->serviceManager->get(static::CONFIG_SERVICE);
        $config[static::VALIDATION_CONFIG_NAMESPACE][static::DEFAULT_MESSAGE_TEMPLATES_TO_REPLACE_VARIABLE][$messageKey] = $validatorClassReference;
        $this->serviceManager()->setService(static::CONFIG_SERVICE, $config);
    }
}
