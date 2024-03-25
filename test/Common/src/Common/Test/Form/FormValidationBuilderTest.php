<?php

declare(strict_types=1);

namespace CommonTest\Test\Form;

use Common\Test\MockeryTestCase;
use Common\Test\Form\FormValidatorBuilder;
use Common\Form\FormValidator;
use Common\Form\Form;
use Laminas\Form\Element\Csrf;
use Laminas\Form\Element;

/**
 * @see FormValidatorBuilder
 */
class FormValidationBuilderTest extends MockeryTestCase
{
    protected const CSRF_FIELD_NAME = 'CSRF_FIELD_NAME';

    /**
     * @var FormValidatorBuilder
     */
    protected $sut;

    /**
     * @test
     */
    public function aValidator_IsCallable(): void
    {
        // Assert
        $this->assertIsCallable(static fn(): self => \Common\Test\Form\FormValidatorBuilder::aValidator());
    }

    /**
     * @test
     * @depends aValidator_IsCallable
     */
    public function aValidator_ReturnsInstanceOfSelf(): void
    {
        // Execute
        $result = FormValidatorBuilder::aValidator();

        // Assert
        $this->assertInstanceOf(FormValidatorBuilder::class, $result);
    }

    /**
     * @test
     */
    public function populateCsrfValidationBeforeValidating_IsCallable(): void
    {
        // Setup
        $this->setUpSut();

        // Assert
        $this->assertIsCallable(fn(): \Common\Test\Form\FormValidatorBuilder => $this->sut->populateCsrfDataBeforeValidating());
    }

    /**
     * @test
     * @depends populateCsrfValidationBeforeValidating_IsCallable
     */
    public function populateCsrfValidationBeforeValidating_ReturnsSelf(): void
    {
        // Setup
        $this->setUpSut();

        // Execute
        $result = $this->sut->populateCsrfDataBeforeValidating();

        // Assert
        $this->assertSame($this->sut, $result);
    }

    /**
     * @test
     */
    public function build_IsCallable(): void
    {
        // Setup
        $this->setUpSut();

        // Assert
        $this->assertIsCallable(fn(): \Common\Form\FormValidator => $this->sut->build());
    }

    /**
     * @test
     * @depends build_IsCallable
     */
    public function build_ReturnsInstanceOfFormValidator(): void
    {
        // Setup
        $this->setUpSut();

        // Execute
        $result = $this->sut->build();

        // Assert
        $this->assertInstanceOf(FormValidator::class, $result);
    }

    /**
     * @test
     * @depends build_ReturnsInstanceOfFormValidator
     */
    public function build_AllowsCsrfValidation(): void
    {
        // Setup
        $this->setUpSut();
        $form = $this->formWithInvalidCsrf();

        // Execute
        $result = $this->sut->build()->isValid($form);

        // Assert
        $this->assertFalse($result);
    }

    /**
     * @test
     * @depends build_ReturnsInstanceOfFormValidator
     * @depends populateCsrfValidationBeforeValidating_ReturnsSelf
     */
    public function build_DisablesCsrf_ForTopLevelCsrfFormElement(): void
    {
        // Setup
        $this->setUpSut();
        $form = $this->formWithInvalidCsrf();

        // Execute
        $result = $this->sut->populateCsrfDataBeforeValidating()->build()->isValid($form);

        // Assert
        $this->assertTrue($result);
    }

    protected function setUpSut()
    {
        $this->sut = new FormValidatorBuilder();
    }

    protected function formWithElement(Element $element): Form
    {
        $instance = new Form();
        $instance->add($element);
        $instance->setData([]);
        return $instance;
    }

    protected function formWithInvalidCsrf(): Form
    {
        return $this->formWithElement(new Csrf(static::CSRF_FIELD_NAME));
    }
}
