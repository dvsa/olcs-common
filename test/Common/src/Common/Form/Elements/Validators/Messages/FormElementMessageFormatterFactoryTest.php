<?php

declare(strict_types=1);

namespace CommonTest\Form\Elements\Validators\Messages;

use Common\Form\Elements\Validators\Messages\FormElementMessageFormatter;
use Common\Form\Elements\Validators\Messages\FormElementMessageFormatterFactory;
use Common\Test\MockeryTestCase;
use Common\Test\MocksServicesTrait;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\ServiceManager\ServiceManager;

/**
 * @see FormElementMessageFormatterFactory
 */
class FormElementMessageFormatterFactoryTest extends MockeryTestCase
{
    use MocksServicesTrait;

    /**
     * @test
     */
    public function createService_IsCallable()
    {
        // Setup
        $sut = $this->setUpSut();

        // Assert
        $this->assertIsCallable([$sut, 'createService']);
    }

    /**
     * @test
     * @depends createService_IsCallable
     */
    public function createService_ReturnsInstanceOfFormElementMessageFormatter()
    {
        // Setup
        $serviceLocator = $this->setUpServiceLocator();
        $sut = $this->setUpSut();

        // Execute
        $result = $sut->createService($serviceLocator);

        // Assert
        $this->assertInstanceOf(FormElementMessageFormatter::class, $result);
    }

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
    public function __invoke_ReturnsInstanceOfFormElementMessageFormatter()
    {
        // Setup
        $serviceLocator = $this->setUpServiceLocator();
        $sut = $this->setUpSut();

        // Execute
        $result = $sut->__invoke($serviceLocator, FormElementMessageFormatter::class);

        // Assert
        $this->assertInstanceOf(FormElementMessageFormatter::class, $result);
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
    }
}
