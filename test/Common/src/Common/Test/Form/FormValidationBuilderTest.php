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
    public function aValidator_IsCallable()
    {
        // Assert
        $this->assertIsCallable([FormValidatorBuilder::class, 'aValidator']);
    }

    /**
     * @test
     * @depends aValidator_IsCallable
     */
    public function aValidator_ReturnsInstanceOfSelf()
    {
        // Execute
        $result = FormValidatorBuilder::aValidator();

        // Assert
        $this->assertInstanceOf(FormValidatorBuilder::class, $result);
    }

    /**
     * @test
     */
    public function populateCsrfValidationBeforeValidating_IsCallable()
    {
        // Setup
        $this->setUpSut();

        // Assert
        $this->assertIsCallable([$this->sut, 'populateCsrfDataBeforeValidating']);
    }

    /**
     * @test
     * @depends populateCsrfValidationBeforeValidating_IsCallable
     */
    public function populateCsrfValidationBeforeValidating_ReturnsSelf()
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
    public function build_IsCallable()
    {
        // Setup
        $this->setUpSut();

        // Assert
        $this->assertIsCallable([$this->sut, 'build']);
    }

    /**
     * @test
     * @depends build_IsCallable
     */
    public function build_ReturnsInstanceOfFormValidator()
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
    public function build_AllowsCsrfValidation()
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
    public function build_DisablesCsrf_ForTopLevelCsrfFormElement()
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

    /**
     * @param Element $element
     * @return Form
     */
    protected function formWithElement(Element $element): Form
    {
        $instance = new Form();
        $instance->add($element);
        $instance->setData([]);
        return $instance;
    }

    /**
     * @return Form
     */
    protected function formWithInvalidCsrf(): Form
    {
        return $this->formWithElement(new Csrf(static::CSRF_FIELD_NAME));
    }
}
