<?php

declare(strict_types=1);

namespace CommonTest\Form\View\Helper;

use Common\Test\MockeryTestCase;
use Common\Form\FormValidator;
use Common\Form\Form;
use Mockery;
use Mockery\MockInterface;

/**
 * @see FormValidator
 */
class FormValidatorTest extends MockeryTestCase
{
    /**
     * @var FormValidator
     */
    protected $sut;

    /**
     * @test
     */
    public function isValid_IsCallable(): void
    {
        // Setup
        $this->setUpSut();

        // Assert
        $this->assertIsCallable(fn(\Laminas\Form\Form $form): bool => $this->sut->isValid($form));
    }

    /**
     * @test
     * @depends isValid_IsCallable
     */
    public function isValid_ReturnsABoolean(): void
    {
        // Setup
        $this->setUpSut();
        $form = $this->formThatIsValid();

        // Assert
        $this->assertIsBool($this->sut->isValid($form));
    }

    /**
     * @test
     * @depends isValid_ReturnsABoolean
     */
    public function isValid_ReturnsABoolean_ThatIsTrueWhenAFormIsValid(): void
    {
        // Setup
        $this->setUpSut();
        $form = $this->formThatIsValid();

        // Assert
        $this->assertTrue($this->sut->isValid($form));
    }

    /**
     * @test
     * @depends isValid_ReturnsABoolean
     */
    public function isValid_ReturnsABoolean_ThatIsFalseWhenAFormIsNotValid(): void
    {
        // Setup
        $this->setUpSut();
        $form = $this->formThatIsInvalid();

        // Assert
        $this->assertFalse($this->sut->isValid($form));
    }

    protected function setUpSut()
    {
        $this->sut = new FormValidator();
    }

    /**
     * @return MockInterface|Form
     */
    protected function form(): MockInterface
    {
        $instance = Mockery::mock(Form::class);
        $instance->shouldIgnoreMissing();
        return $instance;
    }

    /**
     * @return MockInterface|Form
     */
    protected function formThatIsValid(): MockInterface
    {
        $instance = $this->form();
        $instance->allows('isValid')->andReturn(true);
        return $instance;
    }

    /**
     * @return MockInterface|Form
     */
    protected function formThatIsInvalid(): MockInterface
    {
        $instance = $this->form();
        $instance->allows('isValid')->andReturn(false);
        return $instance;
    }
}
