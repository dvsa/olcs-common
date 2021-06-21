<?php

namespace CommonTest\Form\View\Helper;

use Common\Test\MockeryTestCase;
use Laminas\Form\Element\Csrf;
use Common\Form\FormWithCsrfInterface;
use Common\Form\FormWithCsrfTrait;
use Laminas\InputFilter\InputInterface;
use Common\Form\Form;

/**
 * @see FormWithCsrfTrait
 */
class FormWithCsrfTraitTest extends MockeryTestCase
{
    protected const EMPTY_ARRAY_VALUE = [];
    protected const INVALID_CSRF_VALUE = 'AN INVALID CSRF VALUE';
    protected const CSRF_KEY = 'security';
    protected const EMPTY_STRING_VALUE = '';

    /**
     * @var FormWithCsrfInterface|Form
     */
    protected $sut;

    /**
     * @test
     */
    public function getCsrfElement_IsCallable()
    {
        // Setup
        $this->setUpSut();

        // Assert
        $this->assertIsCallable([$this->sut, 'getCsrfElement']);
    }

    /**
     * @test
     * @depends getCsrfElement_IsCallable
     */
    public function getCsrfElement_ReturnsACsrfElement()
    {
        // Setup
        $this->setUpSut();

        // Assert
        $this->assertInstanceOf(Csrf::class, $this->sut->getCsrfElement());
    }

    /**
     * @test
     * @depends getCsrfElement_ReturnsACsrfElement
     */
    public function getCsrfElement_ReturnsACsrfElement_WithAName()
    {
        // Setup
        $this->setUpSut();

        // Assert
        $this->assertEquals(static::CSRF_KEY, $this->sut->getCsrfElement()->getName());
    }

    /**
     * @test
     */
    public function getCsrfInput_IsCallable()
    {
        // Setup
        $this->setUpSut();

        // Assert
        $this->assertIsCallable([$this->sut, 'getCsrfInput']);
    }

    /**
     * @test
     * @depends getCsrfInput_IsCallable
     */
    public function getCsrfInput_ReturnsInstanceOfInput()
    {
        // Setup
        $this->setUpSut();

        // Execute
        $result = $this->sut->getCsrfInput();

        // Assert
        $this->assertInstanceOf(InputInterface::class, $result);
    }

    /**
     * @test
     * @depends getCsrfInput_ReturnsInstanceOfInput
     */
    public function getCsrfInput_ReturnsInstanceOfInput_ThatIsRequired()
    {
        // Setup
        $this->setUpSut();

        // Execute
        $this->sut->setData(static::EMPTY_ARRAY_VALUE);
        $this->sut->isValid();

        // Assert
        $this->assertNotNull($this->sut->getMessages()[static::CSRF_KEY] ?? null);
    }

    /**
     * @test
     * @depends getCsrfInput_ReturnsInstanceOfInput
     */
    public function getCsrfInput_ReturnsInstanceOfInput_ThatAcceptsAValidValue()
    {
        // Setup
        $this->setUpSut();

        // Execute
        $csrfValidator = $this->sut->getCsrfInput()->getValidatorChain()->getValidators()[0]['instance'];
        assert($csrfValidator instanceof \Laminas\Validator\Csrf);
        $this->sut->setData([static::CSRF_KEY => $csrfValidator->getHash()]);
        $this->sut->isValid();

        // Assert
        $this->assertNull($this->sut->getMessages()[static::CSRF_KEY] ?? null);
    }

    /**
     * @return array
     */
    public function csrfInvalidValueDataProvider(): array
    {
        return [
            'non-empty invalid csrf value' => [static::INVALID_CSRF_VALUE],
            'empty csrf value - string' => [static::EMPTY_STRING_VALUE],
            'empty csrf value - null' => [null],
        ];
    }

    /**
     * @param mixed $value
     * @test
     * @depends getCsrfInput_ReturnsInstanceOfInput
     * @dataProvider csrfInvalidValueDataProvider
     */
    public function getCsrfInput_ReturnsInstanceOfInput_ThatRejectsAnInvalidValue($value)
    {
        // Setup
        $this->setUpSut();

        // Execute
        $this->sut->setData([static::CSRF_KEY => $value]);
        $this->sut->isValid();

        // Assert
        $this->assertNotNull($this->sut->getMessages()[static::CSRF_KEY] ?? null);
    }

    protected function setUpSut()
    {
        $this->sut = new class extends Form implements FormWithCsrfInterface {
            use FormWithCsrfTrait;

            public function __construct($name = null)
            {
                parent::__construct($name);
                $this->initialiseCsrf();
            }
        };
    }
}
