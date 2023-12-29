<?php

declare(strict_types=1);

namespace CommonTest\Form\View\Helper;

use Common\Form\Elements\Validators\Messages\FormElementMessageFormatter;
use Common\Form\Elements\Validators\Messages\FormElementMessageFormatterFactory;
use Common\Form\View\Helper\FormElementErrors;
use Common\Form\View\Helper\FormElementErrorsFactory;
use Common\Test\MockeryTestCase;
use Common\Test\MocksServicesTrait;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\ServiceManager\ServiceManager;
use Laminas\Validator\ValidatorPluginManager;

/**
 * @see FormElementErrorsFactory
 */
class FormElementErrorsFactoryTest extends MockeryTestCase
{
    use MocksServicesTrait;

    protected const VALIDATOR_MANAGER = 'ValidatorManager';

    /**
     * @test
     */
    public function __invoke_IsCallable()
    {
        // Setup
        $sut = $this->setUpSut();

        // Assert
        $this->assertIsCallable([$sut, '__invoke']);
    }

    /**
     * @test
     * @depends __invoke_IsCallable
     */
    public function __invoke_ReturnsInstanceOfFormElementErrors()
    {
        // Setup
        $serviceLocator = $this->setUpServiceLocator();
        $pluginManager = $this->setUpAbstractPluginManager($serviceLocator);
        $sut = $this->setUpSut();

        // Execute
        $result = $sut->__invoke($pluginManager, FormElementErrors::class);

        // Assert
        $this->assertInstanceOf(FormElementErrors::class, $result);
    }

    /**
     * @return FormElementErrorsFactory
     */
    protected function setUpSut(): FormElementErrorsFactory
    {
        return new FormElementErrorsFactory();
    }

    /**
     * @param ServiceManager $serviceManager
     */
    protected function setUpDefaultServices(ServiceManager $serviceManager)
    {
        $serviceManager->setService(TranslatorInterface::class, $this->setUpMockService(TranslatorInterface::class));
        $serviceManager->setFactory(FormElementMessageFormatter::class, new FormElementMessageFormatterFactory());
        $serviceManager->setService(static::VALIDATOR_MANAGER, new ValidatorPluginManager());
    }
}
