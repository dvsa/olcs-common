<?php

declare(strict_types=1);

namespace CommonTest\Form\View\Helper;

use Common\Form\View\Helper\FormElementFactory;
use Common\Test\MockeryTestCase;
use Common\Test\MocksServicesTrait;
use Laminas\ServiceManager\ServiceManager;
use Common\Form\View\Helper\FormElement;
use Mockery;
use Laminas\Form\ElementInterface;
use Mockery\MockInterface;
use Laminas\View\Renderer\PhpRenderer;

/**
 * @see FormElementFactory
 */
class FormElementFactoryTest extends MockeryTestCase
{
    use MocksServicesTrait;

    protected const ELEMENT_CLASS = 'ELEMENT_CLASS';
    protected const RENDERER_CLASS = 'RENDERER_CLASS';
    protected const CONFIG_WITH_RENDERER = [
        'form' => [
            'element' => [
                'renderers' => [
                    self::ELEMENT_CLASS => self::RENDERER_CLASS,
                ],
            ],
        ],
    ];
    protected const RENDERED_STRING = 'RENDERED STRING';

    /**
     * @var FormElementFactory|null
     */
    protected $sut;

    /**
     * @test
     */
    public function createService_IsCallable()
    {
        // Setup
        $this->setUpSut();

        // Assert
        $this->assertIsCallable([$this->sut, 'createService']);
    }

    /**
     * @test
     * @depends createService_IsCallable
     */
    public function createService_ReturnsInstanceOfFormElement()
    {
        // Setup
        $this->setUpSut();

        // Execute
        $result = $this->sut->createService($this->setUpAbstractPluginManager($this->serviceManager));

        // Assert
        $this->assertInstanceOf(FormElement::class, $result);
    }

    /**
     * @test
     * @depends createService_ReturnsInstanceOfFormElement
     */
    public function createService_RegistersElementRenderer()
    {
        // Setup
        $this->setUpSut();
        $this->serviceManager->setService('config', static::CONFIG_WITH_RENDERER);

        $result = $this->sut->createService($this->setUpAbstractPluginManager($this->serviceManager));
        assert($result instanceof FormElement, 'Expected instance of FormElement');
        $result->setView($this->renderer());

        // Expect
        $this->renderer()->expects('plugin')->with(self::RENDERER_CLASS)->andReturn($this->helper());

        // Execute
        $result->render($this->elementWithClass(static::ELEMENT_CLASS));
    }

    /**
     * @test
     */
    public function __invoke_IsCallable()
    {
        // Setup
        $this->setUpSut();

        // Assert
        $this->assertIsCallable([$this->sut, '__invoke']);
    }

    /**
     * @test
     * @depends __invoke_IsCallable
     */
    public function __invoke_ReturnsInstanceOfFormElement()
    {
        // Setup
        $this->setUpSut();

        // Execute
        $result = $this->sut->__invoke($this->setUpAbstractPluginManager($this->serviceManager), null);

        // Assert
        $this->assertInstanceOf(FormElement::class, $result);
    }

    /**
     * @test
     * @depends __invoke_ReturnsInstanceOfFormElement
     */
    public function __invoke_RegistersElementRenderer()
    {
        // Setup
        $this->setUpSut();
        $this->serviceManager->setService('config', static::CONFIG_WITH_RENDERER);

        $result = $this->sut->__invoke($this->setUpAbstractPluginManager($this->serviceManager), null);
        assert($result instanceof FormElement, 'Expected instance of FormElement');
        $result->setView($this->renderer());

        // Expect
        $this->renderer()->expects('plugin')->with(self::RENDERER_CLASS)->andReturn($this->helper());

        // Execute
        $result->render($this->elementWithClass(static::ELEMENT_CLASS));
    }

    protected function setUp(): void
    {
        $this->setUpServiceManager();
    }

    protected function setUpSut()
    {
        $this->sut = new FormElementFactory();
    }

    protected function setUpDefaultServices()
    {
        $this->serviceManager->setService('config', []);
        $this->renderer();
    }

    /**
     * @return MockInterface|PhpRenderer
     */
    protected function renderer(): MockInterface
    {
        if (! $this->serviceManager->has(PhpRenderer::class)) {
            $instance = $this->setUpMockService(PhpRenderer::class);
            $instance->allows('plugin')->with(FormElement::DEFAULT_HELPER)->andReturn($this->helper());
            $this->serviceManager->setService(PhpRenderer::class, $instance);
        }
        return $this->serviceManager->get(PhpRenderer::class);
    }

    /**
     * @param string $className
     * @return MockInterface
     */
    protected function elementWithClass(string $className): MockInterface
    {
        $element = Mockery::mock($className, ElementInterface::class);
        $element->shouldIgnoreMissing();
        return $element;
    }

    /**
     * @return MockInterface
     */
    protected function helper(): MockInterface
    {
        $helper = Mockery::mock(\Closure::fromCallable(function () {}));
        $helper->allows('__invoke')->andReturn(static::RENDERED_STRING);
        return $helper;
    }
}
