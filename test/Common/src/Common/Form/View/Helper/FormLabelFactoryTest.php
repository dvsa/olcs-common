<?php

declare(strict_types=1);

namespace CommonTest\Form\View\Helper;

use Common\Form\View\Helper\Extended\FormLabel;
use Common\Form\View\Helper\Extended\FormLabelFactory;
use Common\Form\View\Helper\FormErrors;
use Common\Test\MockeryTestCase;
use Common\Test\MocksServicesTrait;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\ServiceManager\ServiceManager;

class FormLabelFactoryTest extends MockeryTestCase
{
    use MocksServicesTrait;

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
    public function __invoke_ReturnsInstanceOfFormLabel()
    {
        // Setup
        $serviceLocator = $this->setUpServiceLocator();
        $sut = $this->setUpSut();

        // Execute
        $result = $sut->__invoke($serviceLocator, FormErrors::class);

        // Assert
        $this->assertInstanceOf(FormLabel::class, $result);
    }

    /**
     * @return FormLabelFactory
     */
    protected function setUpSut(): FormLabelFactory
    {
        return new FormLabelFactory();
    }

    /**
     * @param ServiceManager $serviceManager
     */
    protected function setUpDefaultServices(ServiceManager $serviceManager)
    {
        $serviceManager->setService(TranslatorInterface::class, $this->setUpMockService(TranslatorInterface::class));
    }
}
