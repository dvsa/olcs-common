<?php

declare(strict_types=1);

namespace CommonTest\Form\View\Helper;

use Common\Form\Elements\Validators\Messages\FormElementMessageFormatter;
use Common\Form\Elements\Validators\Messages\FormElementMessageFormatterFactory;
use Common\Form\View\Helper\FormErrors;
use Common\Form\View\Helper\FormErrorsFactory;
use Common\Test\MockeryTestCase;
use Common\Test\MocksServicesTrait;
use Laminas\I18n\Translator\TranslatorInterface;
use HTMLPurifier;
use Laminas\ServiceManager\ServiceManager;

/**
 * @see FormErrorsFactory
 */
class FormErrorsFactoryTest extends MockeryTestCase
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
    public function createService_ReturnsInstanceOfFormErrors()
    {
        // Setup
        $serviceLocator = $this->setUpServiceLocator();
        $pluginManager = $this->setUpAbstractPluginManager($serviceLocator);
        $sut = $this->setUpSut();

        // Execute
        $result = $sut->createService($pluginManager);

        // Assert
        $this->assertInstanceOf(FormErrors::class, $result);
    }

    /**
     * @test
     */
    public function __invoke_IsCallable()
    {
        // Setup
        $sut = $this->setUpSut();

        // Assert
        $this->assertIsCallable([$sut, 'createService']);
    }

    /**
     * @test
     * @depends __invoke_IsCallable
     */
    public function __invoke_ReturnsInstanceOfFormErrors()
    {
        // Setup
        $serviceLocator = $this->setUpServiceLocator();
        $pluginManager = $this->setUpAbstractPluginManager($serviceLocator);
        $sut = $this->setUpSut();

        // Execute
        $result = $sut->__invoke($pluginManager, FormErrors::class);

        // Assert
        $this->assertInstanceOf(FormErrors::class, $result);
    }

    /**
     * @return FormErrorsFactory
     */
    protected function setUpSut(): FormErrorsFactory
    {
        return new FormErrorsFactory();
    }

    /**
     * @param ServiceManager $serviceManager
     */
    protected function setUpDefaultServices(ServiceManager $serviceManager)
    {
        $serviceManager->setService(TranslatorInterface::class, $this->setUpMockService(TranslatorInterface::class));
        $serviceManager->setService(HTMLPurifier::class, new HTMLPurifier());
        $serviceManager->setFactory(FormElementMessageFormatter::class, new FormElementMessageFormatterFactory());
    }
}
