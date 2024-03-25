<?php

declare(strict_types=1);

namespace CommonTest\InputFilter;

use Common\Test\MockeryTestCase;
use Common\InputFilter\ChainValidatedInput;
use InvalidArgumentException;
use Laminas\Validator\NotEmpty;
use Laminas\Validator\ValidatorChain;
use Laminas\Validator\ValidatorInterface;
use Laminas\Filter\FilterChain;
use Mockery as m;
use Laminas\InputFilter\Input;

/**
 * @see ChainValidatedInput
 */
class ChainValidatedInputTest extends MockeryTestCase
{
    protected const AN_INPUT_NAME = 'AN INPUT NAME';

    protected const A_CUSTOM_INPUT_NAME = 'A CUSTOM INPUT NAME';

    protected const AN_ALTERNATIVE_INPUT_NAME = 'AN ALTERNATIVE INPUT NAME';

    protected const EXPECTED_STRING_EXCEPTION_MESSAGE = 'Expected string';

    protected const AN_INT = 0;

    protected const REQUIRED = true;

    protected const NOT_REQUIRED = false;

    protected const EXPECTED_BOOL_EXCEPTION_MESSAGE = 'Expected bool';

    protected const ALLOW_EMPTY = true;

    protected const DONT_ALLOW_EMPTY = false;

    protected const BREAK_ON_FAILURE = true;

    protected const DONT_BREAK_ON_FAILURE = false;

    protected const A_CUSTOM_ERROR_MESSAGE = 'AN ERROR MESSAGE';

    protected const A_MESSAGES_ARRAY_CONTAINING_A_CUSTOM_ERROR_MESSAGE = [self::A_CUSTOM_ERROR_MESSAGE];

    protected const NO_ERROR_MESSAGE = null;

    protected const THE_DEFAULT_INPUT_VALUE = null;

    protected const A_RAW_INPUT_VALUE = 'A RAW INPUT VALUE';

    protected const A_SECOND_RAW_INPUT_VALUE = 'A SECOND RAW INPUT VALUE';

    protected const A_FILTERED_INPUT_VALUE = 'A FILTERED INPUT VALUE';

    protected const EMPTY_VALIDATION_CONTEXT = [];

    protected const VALID = true;

    protected const NOT_VALID = false;

    protected const MESSAGES_FOR_A_VALID_INPUT = [];

    protected const MESSAGES_FOR_AN_INVALID_INPUT = ['A VALIDATOR KEY' => 'AN VALIDATION MESSAGE'];

    protected const THE_DEFAULT_ERROR_MESSAGE = null;

    protected const A_STRING_SUFFIX = 'A STRING SUFFIX';

    protected const A_SECOND_STRING_SUFFIX = 'A SECOND STRING SUFFIX';

    protected const AN_EMPTY_RAW_INPUT_VALUE = '';

    /**
     * @var ChainValidatedInput|null
     */
    protected $sut;

    /**
     * @test
     */
    public function getName_IsCallable(): void
    {
        // Setup
        $this->setUpSut();

        // Assert
        $this->assertIsCallable([$this->sut, 'getName']);
    }

    /**
     * @test
     * @depends getName_IsCallable
     */
    public function getName_ReturnsTheNameOfAnInput(): void
    {
        // Setup
        $this->setUpSut(static::AN_INPUT_NAME);

        // Assert
        $this->assertSame(static::AN_INPUT_NAME, $this->sut->getName());
    }

    /**
     * @test
     */
    public function setName_IsCallable(): void
    {
        // Setup
        $this->setUpSut();

        // Assert
        $this->assertIsCallable([$this->sut, 'setName']);
    }

    /**
     * @test
     * @depends setName_IsCallable
     */
    public function setName_ReturnsSelf(): void
    {
        // Setup
        $this->setUpSut();

        // Assert
        $this->assertSame($this->sut, $this->sut->setName(static::AN_INPUT_NAME));
    }

    /**
     * @test
     * @depends setName_IsCallable
     * @depends getName_ReturnsTheNameOfAnInput
     */
    public function setName_SetsTheName(): void
    {
        // Setup
        $this->setUpSut();

        // Execute
        $this->sut->setName(static::AN_ALTERNATIVE_INPUT_NAME);

        // Assert
        $this->assertSame(static::AN_ALTERNATIVE_INPUT_NAME, $this->sut->getName());
    }

    /**
     * @test
     * @depends setName_IsCallable
     * @depends getName_ReturnsTheNameOfAnInput
     */
    public function setName_ThrowsInvalidArgumentException_IfNotPassedAString(): void
    {
        // Setup
        $this->setUpSut();

        // Expect
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(static::EXPECTED_STRING_EXCEPTION_MESSAGE);

        // Execute
        $this->sut->setName(static::AN_INT);
    }

    /**
     * @test
     */
    public function isRequired_IsCallable(): void
    {
        // Setup
        $this->setUpSut();

        // Assert
        $this->assertIsCallable([$this->sut, 'isRequired']);
    }

    /**
     * @test
     * @depends isRequired_IsCallable
     */
    public function isRequired_ReturnsABoolean(): void
    {
        // Setup
        $this->setUpSut();

        // Execute
        $result = $this->sut->isRequired();

        // Assert
        $this->assertIsBool($result);
    }

    /**
     * @test
     * @depends isRequired_ReturnsABoolean
     */
    public function isRequired_ReturnsTrueByDefault(): void
    {
        // Setup
        $this->setUpSut();

        // Execute
        $result = $this->sut->isRequired();

        // Assert
        $this->assertSame(true, $result);
    }


    /**
     * @test
     */
    public function setRequired_IsCallable(): void
    {
        // Setup
        $this->setUpSut();

        // Assert
        $this->assertIsCallable([$this->sut, 'setRequired']);
    }

    /**
     * @test
     * @depends setRequired_IsCallable
     */
    public function setRequired_ReturnsSelf(): void
    {
        // Setup
        $this->setUpSut();

        // Execute
        $result = $this->sut->setRequired(static::REQUIRED);

        // Assert
        $this->assertSame($this->sut, $result);
    }

    /**
     * @test
     * @depends setRequired_IsCallable
     */
    public function setRequired_ThrowsInvalidArgumentException_IfProvidedNonBoolean(): void
    {
        // Setup
        $this->setUpSut();

        // Expect
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(static::EXPECTED_BOOL_EXCEPTION_MESSAGE);

        // Execute
        $this->sut->setRequired(static::AN_INT);
    }

    /**
     * @test
     * @depends setRequired_IsCallable
     * @depends isRequired_ReturnsABoolean
     */
    public function setRequired_SetsAnInputAsRequired(): void
    {
        // Setup
        $this->setUpSut();

        // Execute
        $this->sut->setRequired(static::REQUIRED);

        // Assert
        $this->assertSame(static::REQUIRED, $this->sut->isRequired());
    }

    /**
     * @test
     * @depends setRequired_IsCallable
     * @depends isRequired_ReturnsABoolean
     */
    public function setRequired_SetsAnInputAsNotRequired(): void
    {
        // Setup
        $this->setUpSut();

        // Execute
        $this->sut->setRequired(static::NOT_REQUIRED);

        // Assert
        $this->assertSame(static::NOT_REQUIRED, $this->sut->isRequired());
    }

    /**
     * @test
     */
    public function getValidatorChain_IsCallable(): void
    {
        // Setup
        $this->setUpSut();

        // Assert
        $this->assertIsCallable([$this->sut, 'getValidatorChain']);
    }


    /**
     * @test
     * @depends getValidatorChain_IsCallable
     */
    public function getValidatorChain_ReturnsAValidatorChain(): void
    {
        // Setup
        $this->setUpSut();

        // Assert
        $this->assertInstanceOf(ValidatorChain::class, $this->sut->getValidatorChain());
    }

    /**
     * @test
     * @depends getValidatorChain_ReturnsAValidatorChain
     */
    public function getValidatorChain_ThatIsEmpty_ByDefault(): void
    {
        // Setup
        $this->setUpSut();

        // Execute
        $result = $this->sut->getValidatorChain();

        // Assert
        $this->assertEmpty($result->getValidators());
    }

    /**
     * @test
     */
    public function setValidatorChain_IsCallable(): void
    {
        // Setup
        $this->setUpSut();

        // Assert
        $this->assertIsCallable([$this->sut, 'setValidatorChain']);
    }

    /**
     * @test
     * @depends setValidatorChain_IsCallable
     */
    public function setValidatorChain_ReturnsSelf(): void
    {
        // Setup
        $this->setUpSut();

        // Execute
        $result = $this->sut->setValidatorChain($this->emptyValidatorChain());

        // Assert
        $this->assertSame($this->sut, $result);
    }

    /**
     * @test
     * @depends setValidatorChain_IsCallable
     * @depends getValidatorChain_IsCallable
     */
    public function setValidatorChain_SetsTheValidatorChain(): void
    {
        // Setup
        $this->setUpSut();
        $newValidatorChain = $this->emptyValidatorChain();

        // Execute
        $this->sut->setValidatorChain($newValidatorChain);

        // Assert
        $this->assertSame($newValidatorChain, $this->sut->getValidatorChain());
    }

    /**
     * @test
     */
    public function allowEmpty_IsCallable(): void
    {
        // Setup
        $this->setUpSut();

        // Assert
        $this->assertIsCallable([$this->sut, 'allowEmpty']);
    }

    /**
     * @test
     * @depends allowEmpty_IsCallable
     * @depends getValidatorChain_ReturnsAValidatorChain
     */
    public function allowEmpty_ReturnsTrue_IfNotEmptyValidatorExistsInTheValidatorChain(): void
    {
        // Setup
        $this->setUpSut();
        $validatorChain = $this->sut->getValidatorChain();
        $validatorChain->attach(new NotEmpty());

        // Assert
        $this->assertSame(static::ALLOW_EMPTY, $this->sut->allowEmpty());
    }

    /**
     * @test
     * @depends allowEmpty_IsCallable
     * @depends setValidatorChain_SetsTheValidatorChain
     */
    public function allowEmpty_ReturnsFalse_IfNotEmptyValidatorDoesNotExistInTheValidatorChain(): void
    {
        // Setup
        $this->setUpSut();
        $this->sut->setValidatorChain($this->emptyValidatorChain());

        // Assert
        $this->assertSame(static::DONT_ALLOW_EMPTY, $this->sut->allowEmpty());
    }

    /**
     * @test
     */
    public function setAllowEmpty_IsCallable(): void
    {
        // Setup
        $this->setUpSut();

        // Assert
        $this->assertIsCallable([$this->sut, 'setAllowEmpty']);
    }

    /**
     * @test
     * @depends setAllowEmpty_IsCallable
     */
    public function setAllowEmpty_ReturnsSelf(): void
    {
        // Setup
        $this->setUpSut();

        // Execute
        $result = $this->sut->setAllowEmpty(static::ALLOW_EMPTY);

        // Assert
        $this->assertSame($this->sut, $result);
    }

    /**
     * @test
     * @depends setAllowEmpty_IsCallable
     */
    public function setAllowEmpty_ThrowsInvalidArgumentException_IfNotPassedABool(): void
    {
        // Setup
        $this->setUpSut();

        // Expect
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(static::EXPECTED_BOOL_EXCEPTION_MESSAGE);

        // Execute
        $this->sut->setAllowEmpty(static::AN_INT);
    }

    /**
     * @test
     * @depends setAllowEmpty_IsCallable
     * @depends allowEmpty_ReturnsTrue_IfNotEmptyValidatorExistsInTheValidatorChain
     * @depends allowEmpty_ReturnsFalse_IfNotEmptyValidatorDoesNotExistInTheValidatorChain
     */
    public function setAllowEmpty_WhenPassedFalse_AndNotEmptyValidatorIsNotPresentInTheValidatorChain_AddsNotEmptyValidatorToTheValidatorChain(): void
    {
        // Setup
        $this->setUpSut();

        // Execute
        $this->sut->setAllowEmpty(static::DONT_ALLOW_EMPTY);

        // Assert
        $this->assertNotNull($this->getNotEmptyValidatorFromValidatorChain($this->sut->getValidatorChain()));
    }

    /**
     * @test
     * @depends setAllowEmpty_IsCallable
     * @depends allowEmpty_ReturnsTrue_IfNotEmptyValidatorExistsInTheValidatorChain
     * @depends allowEmpty_ReturnsFalse_IfNotEmptyValidatorDoesNotExistInTheValidatorChain
     * @depends setValidatorChain_SetsTheValidatorChain
     */
    public function setAllowEmpty_WhenPassedFalse_AndNotEmptyValidatorIsPresentInTheValidatorChain_LeavesNotEmptyValidator(): void
    {
        // Setup
        $this->setUpSut();
        $notEmptyValidator = new NotEmpty();
        $this->sut->setValidatorChain($this->validatorChainWithValidator($notEmptyValidator));

        // Execute
        $this->sut->setAllowEmpty(static::DONT_ALLOW_EMPTY);

        // Assert
        $this->assertSame($notEmptyValidator, $this->getNotEmptyValidatorFromValidatorChain($this->sut->getValidatorChain()));
    }

    /**
     * @test
     * @depends setAllowEmpty_IsCallable
     * @depends allowEmpty_ReturnsTrue_IfNotEmptyValidatorExistsInTheValidatorChain
     * @depends allowEmpty_ReturnsFalse_IfNotEmptyValidatorDoesNotExistInTheValidatorChain
     * @depends setValidatorChain_SetsTheValidatorChain
     */
    public function setAllowEmpty_WhenPassedTrue_AndNotEmptyValidatorIsPresentInTheValidatorChain_RemovesNotEmptyValidatorFromTheValidatorChain(): void
    {
        // Setup
        $this->setUpSut();
        $notEmptyValidator = new NotEmpty();
        $this->sut->setValidatorChain($this->validatorChainWithValidator($notEmptyValidator));

        // Execute
        $this->sut->setAllowEmpty(static::ALLOW_EMPTY);

        // Assert
        $this->assertNull($this->getNotEmptyValidatorFromValidatorChain($this->sut->getValidatorChain()));
    }

    /**
     * @test
     * @depends setAllowEmpty_IsCallable
     * @depends allowEmpty_ReturnsTrue_IfNotEmptyValidatorExistsInTheValidatorChain
     * @depends allowEmpty_ReturnsFalse_IfNotEmptyValidatorDoesNotExistInTheValidatorChain
     * @depends setValidatorChain_SetsTheValidatorChain
     */
    public function setAllowEmpty_WhenPassedTrue_AndNotEmptyValidatorIsNotPresentInTheValidatorChain_DoesNotAddNotEmptyValidator(): void
    {
        // Setup
        $this->setUpSut();
        $this->sut->setValidatorChain($this->emptyValidatorChain());

        // Execute
        $this->sut->setAllowEmpty(static::ALLOW_EMPTY);

        // Assert
        $this->assertNull($this->getNotEmptyValidatorFromValidatorChain($this->sut->getValidatorChain()));
    }

    /**
     * @test
     */
    public function breakOnFailure_IsCallable(): void
    {
        // Setup
        $this->setUpSut();

        // Assert
        $this->assertIsCallable([$this->sut, 'breakOnFailure']);
    }

    /**
     * @test
     * @depends breakOnFailure_IsCallable
     */
    public function breakOnFailure_ReturnsFalse_ByDefault(): void
    {
        // Setup
        $this->setUpSut();

        // Assert
        $this->assertEquals(static::DONT_BREAK_ON_FAILURE, $this->sut->breakOnFailure());
    }

    /**
     * @test
     */
    public function setBreakOnFailure_IsCallable(): void
    {
        // Setup
        $this->setUpSut();

        // Assert
        $this->assertIsCallable([$this->sut, 'setBreakOnFailure']);
    }

    /**
     * @test
     * @depends setBreakOnFailure_IsCallable
     */
    public function setBreakOnFailure_ThrowsInvalidArgumentException_IfNotPassedABool(): void
    {
        // Setup
        $this->setUpSut();

        // Expect
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(static::EXPECTED_BOOL_EXCEPTION_MESSAGE);

        // Execute
        $this->sut->setBreakOnFailure(static::AN_INT);
    }

    /**
     * @test
     * @depends setBreakOnFailure_IsCallable
     * @depends breakOnFailure_IsCallable
     */
    public function setBreakOnFailure_SetsTheBreakOnFailureFlagToTrue(): void
    {
        // Setup
        $this->setUpSut();

        // Execute
        $this->sut->setBreakOnFailure(static::BREAK_ON_FAILURE);

        // Assert
        $this->assertSame(static::BREAK_ON_FAILURE, $this->sut->breakOnFailure());
    }

    /**
     * @test
     */
    public function getErrorMessage_IsCallable(): void
    {
        // Setup
        $this->setUpSut();

        // Assert
        $this->assertIsCallable([$this->sut, 'getErrorMessage']);
    }

    /**
     * @test
     * @depends getErrorMessage_IsCallable
     */
    public function getErrorMessage_ReturnsNullByDefault(): void
    {
        // Setup
        $this->setUpSut();

        // Execute
        $result = $this->sut->getErrorMessage();

        // Assert
        $this->assertSame(static::THE_DEFAULT_ERROR_MESSAGE, $result);
    }

    /**
     * @test
     */
    public function setErrorMessage_IsCallable(): void
    {
        // Setup
        $this->setUpSut();

        // Assert
        $this->assertIsCallable([$this->sut, 'setErrorMessage']);
    }

    /**
     * @test
     * @depends getErrorMessage_IsCallable
     * @depends setErrorMessage_IsCallable
     */
    public function setErrorMessage_WhenProvidedAString_SetsTheString(): void
    {
        // Setup
        $this->setUpSut();

        // Execute
        $this->sut->setErrorMessage(static::A_CUSTOM_ERROR_MESSAGE);

        // Assert
        $this->assertSame(static::A_CUSTOM_ERROR_MESSAGE, $this->sut->getErrorMessage());
    }

    /**
     * @test
     * @depends getErrorMessage_IsCallable
     * @depends setErrorMessage_IsCallable
     */
    public function setErrorMessage_WhenProvidedNull_SetsNull(): void
    {
        // Setup
        $this->setUpSut();

        // Execute
        $this->sut->setErrorMessage(static::NO_ERROR_MESSAGE);

        // Assert
        $this->assertSame(static::NO_ERROR_MESSAGE, $this->sut->getErrorMessage());
    }

    /**
     * @test
     * @depends getErrorMessage_IsCallable
     * @depends setErrorMessage_IsCallable
     */
    public function setErrorMessage_WhenProvidedAnObject_SetsStringRepresentationOfAnObject(): void
    {
        // Setup
        $this->setUpSut();

        // Execute
        $this->sut->setErrorMessage($this->errorMessageObjectThatCastsToAString(static::A_CUSTOM_ERROR_MESSAGE));

        // Assert
        $this->assertSame(static::A_CUSTOM_ERROR_MESSAGE, $this->sut->getErrorMessage());
    }

    /**
     * @test
     */
    public function getFilterChain_IsCallable(): void
    {
        // Setup
        $this->setUpSut();

        // Assert
        $this->assertIsCallable([$this->sut, 'getFilterChain']);
    }

    /**
     * @test
     */
    public function getFilterChain_ReturnsAFilterChain(): void
    {
        // Setup
        $this->setUpSut();

        // Execute
        $result = $this->sut->getFilterChain();

        // Assert
        $this->assertInstanceOf(FilterChain::class, $result);
    }

    /**
     * @test
     */
    public function setFilterChain_IsCallable(): void
    {
        // Setup
        $this->setUpSut();

        // Assert
        $this->assertIsCallable([$this->sut, 'setFilterChain']);
    }

    /**
     * @test
     * @depends setFilterChain_IsCallable
     */
    public function setFilterChain_ReturnsSelf(): void
    {
        // Setup
        $this->setUpSut();
        $filterChain = new FilterChain();

        // Execute
        $result = $this->sut->setFilterChain($filterChain);

        // Assert
        $this->assertSame($this->sut, $result);
    }

    /**
     * @test
     * @depends setFilterChain_IsCallable
     * @depends getFilterChain_ReturnsAFilterChain
     */
    public function setFilterChain_SetsAFilterChain(): void
    {
        // Setup
        $this->setUpSut();
        $filterChain = new FilterChain();

        // Execute
        $this->sut->setFilterChain($filterChain);

        // Assert
        $this->assertSame($filterChain, $this->sut->getFilterChain());
    }

    /**
     * @test
     */
    public function getRawValue_IsCallable(): void
    {
        // Setup
        $this->setUpSut();

        // Assert
        $this->assertIsCallable([$this->sut, 'getRawValue']);
    }

    /**
     * @test
     * @depends getRawValue_IsCallable
     */
    public function getRawValue_ReturnsNullByDefault(): void
    {
        // Setup
        $this->setUpSut();

        // Execute
        $result = $this->sut->getRawValue();

        // Assert
        $this->assertSame(static::THE_DEFAULT_INPUT_VALUE, $result);
    }

    /**
     * @test
     * @depends getRawValue_IsCallable
     * @depends setFilterChain_SetsAFilterChain
     */
    public function getRawValue_ReturnsTheInputValue_WithoutTheFilterChainApplied(): void
    {
        // Setup
        $this->setUpSut();
        $this->sut->setFilterChain($this->filterChainThatConvertsAllValuesTo(static::A_RAW_INPUT_VALUE));

        // Execute
        $result = $this->sut->getRawValue();

        // Assert
        $this->assertSame(static::THE_DEFAULT_INPUT_VALUE, $result);
    }

    /**
     * @test
     */
    public function getValue_IsCallable(): void
    {
        // Setup
        $this->setUpSut();

        // Assert
        $this->assertIsCallable([$this->sut, 'getValue']);
    }

    /**
     * @test
     */
    public function getValue_ReturnsNullByDefault(): void
    {
        // Setup
        $this->setUpSut();

        // Execute
        $result = $this->sut->getValue();

        // Assert
        $this->assertSame(static::THE_DEFAULT_INPUT_VALUE, $result);
    }

    /**
     * @test
     * @depends getValue_IsCallable
     * @depends setFilterChain_SetsAFilterChain
     */
    public function getValue_ReturnsTheInputValue_WithTheFilterChainApplied(): void
    {
        // Setup
        $this->setUpSut();
        $this->sut->setFilterChain($this->filterChainThatConvertsAllValuesTo(static::A_FILTERED_INPUT_VALUE));

        // Execute
        $result = $this->sut->getValue();

        // Assert
        $this->assertSame(static::A_FILTERED_INPUT_VALUE, $result);
    }

    /**
     * @test
     */
    public function setValue_IsCallable(): void
    {
        // Setup
        $this->setUpSut();

        // Assert
        $this->assertIsCallable([$this->sut, 'setValue']);
    }

    /**
     * @test
     * @depends setValue_IsCallable
     */
    public function setValue_ReturnsSelf(): void
    {
        // Setup
        $this->setUpSut();

        // Execute
        $result = $this->sut->setValue(static::A_RAW_INPUT_VALUE);

        // Assert
        $this->assertSame($this->sut, $result);
    }

    /**
     * @test
     * @depends setValue_IsCallable
     * @depends getRawValue_ReturnsTheInputValue_WithoutTheFilterChainApplied
     */
    public function setValue_SetsAValue(): void
    {
        // Setup
        $this->setUpSut();

        // Execute
        $this->sut->setValue(static::A_RAW_INPUT_VALUE);
        $result = $this->sut->getRawValue();

        // Assert
        $this->assertSame(static::A_RAW_INPUT_VALUE, $result);
    }

    /**
     * @test
     */
    public function isValid_IsCallable(): void
    {
        // Setup
        $this->setUpSut();

        // Assert
        $this->assertIsCallable([$this->sut, 'isValid']);
    }

    /**
     * @test
     */
    public function isValid_ReturnsBoolean(): void
    {
        // Setup
        $this->setUpSut();

        // Assert
        $this->assertIsBool($this->sut->isValid());
    }

    /**
     * @test
     */
    public function isValid_ReturnsTrueByDefault(): void
    {
        // Setup
        $this->setUpSut();

        // Assert
        $this->assertSame(static::VALID, $this->sut->isValid());
    }

    /**
     * @test
     * @depends isValid_ReturnsBoolean
     * @depends setValidatorChain_SetsTheValidatorChain
     * @depends setValue_SetsAValue
     * @depends setFilterChain_SetsAFilterChain
     */
    public function isValid_ProxiesToValidatorChain(): void
    {
        // Setup
        $this->setUpSut();
        $mockValidatorChain = m::mock(ValidatorChain::class)->shouldIgnoreMissing();
        $this->sut->setValidatorChain($mockValidatorChain);
        $this->sut->setValue(static::A_RAW_INPUT_VALUE);
        $this->sut->setFilterChain($this->filterChainThatConvertsAllValuesTo(static::A_FILTERED_INPUT_VALUE));

        // Expect
        $mockValidatorChain->expects('isValid')->withArgs(function ($value, $context) {
            $this->assertSame(static::A_FILTERED_INPUT_VALUE, $value);
            $this->assertSame(static::EMPTY_VALIDATION_CONTEXT, $context);
            return true;
        })->andReturn(static::NOT_VALID);

        // Execute
        $result = $this->sut->isValid(static::EMPTY_VALIDATION_CONTEXT);

        // Assert
        $this->assertSame(static::NOT_VALID, $result);
    }

    /**
     * @test
     */
    public function getMessages_IsCallable(): void
    {
        // Setup
        $this->setUpSut();

        // Assert
        $this->assertIsCallable([$this->sut, 'getMessages']);
    }

    /**
     * @test
     * @depends getMessages_IsCallable
     */
    public function getMessages_ReturnsAnArray(): void
    {
        // Setup
        $this->setUpSut();

        // Execute
        $result = $this->sut->getMessages();

        // Assert
        $this->assertIsArray($result);
    }

    /**
     * @test
     * @depends getMessages_ReturnsAnArray
     */
    public function getMessages_ReturnsAnEmptyArrayByDefault(): void
    {
        // Setup
        $this->setUpSut();

        // Execute
        $result = $this->sut->getMessages();

        // Assert
        $this->assertSame(static::MESSAGES_FOR_A_VALID_INPUT, $result);
    }

    /**
     * @test
     * @depends getMessages_ReturnsAnArray
     * @depends setValidatorChain_SetsTheValidatorChain
     */
    public function getMessages_ReturnsMessagesFromValidatorChain(): void
    {
        // Setup
        $this->setUpSut();
        $mockValidatorChain = m::mock(ValidatorChain::class)->shouldIgnoreMissing();
        $mockValidatorChain->allows('getMessages')->andReturn(static::MESSAGES_FOR_AN_INVALID_INPUT);
        $this->sut->setValidatorChain($mockValidatorChain);

        // Execute
        $result = $this->sut->getMessages();

        // Assert
        $this->assertSame(static::MESSAGES_FOR_AN_INVALID_INPUT, $result);
    }

    /**
     * @test
     * @depends getMessages_ReturnsAnArray
     * @depends setErrorMessage_WhenProvidedAString_SetsTheString
     */
    public function getMessages_ReturnsAnArray_WithCustomErrorMessage(): void
    {
        // Setup
        $this->setUpSut();
        $this->sut->setErrorMessage(static::A_CUSTOM_ERROR_MESSAGE);

        // Execute
        $result = $this->sut->getMessages();

        // Assert
        $this->assertSame(static::A_MESSAGES_ARRAY_CONTAINING_A_CUSTOM_ERROR_MESSAGE, $result);
    }

    /**
     * @test
     * @depends getMessages_ReturnsAnArray_WithCustomErrorMessage
     */
    public function getMessages_ReturnsAnArray_WithCustomErrorMessage_WhenThereAreValidationMessagesFromTheChain(): void
    {
        // Setup
        $this->setUpSut();
        $this->sut->setValidatorChain($this->validatorChainWithErrorMessage());
        $this->sut->setErrorMessage(static::A_CUSTOM_ERROR_MESSAGE);

        // Execute
        $result = $this->sut->getMessages();

        // Assert
        $this->assertSame(static::A_MESSAGES_ARRAY_CONTAINING_A_CUSTOM_ERROR_MESSAGE, $result);
    }

    /**
     * @test
     */
    public function merge_IsCallable(): void
    {
        // Setup
        $this->setUpSut();

        // Assert
        $this->assertIsCallable([$this->sut, 'merge']);
    }

    /**
     * @test
     * @depends merge_IsCallable
     */
    public function merge_ReturnsSelf(): void
    {
        // Setup
        $this->setUpSut();
        $inputToMerge = new ChainValidatedInput(static::AN_INPUT_NAME);

        // Execute
        $result = $this->sut->merge($inputToMerge);

        // Assert
        $this->assertSame($this->sut, $result);
    }

    /**
     * @test
     * @depends merge_IsCallable
     * @depends setBreakOnFailure_SetsTheBreakOnFailureFlagToTrue
     */
    public function merge_SetsBreakOnFailure(): void
    {
        // Setup
        $this->setUpSut();
        $inputToMerge = new ChainValidatedInput(static::AN_INPUT_NAME);
        $inputToMerge->setBreakOnFailure(static::BREAK_ON_FAILURE);

        // Execute
        $this->sut->merge($inputToMerge);
        $result = $this->sut->breakOnFailure();

        // Assert
        $this->assertSame(static::BREAK_ON_FAILURE, $result);
    }

    /**
     * @test
     * @depends merge_IsCallable
     * @depends setErrorMessage_WhenProvidedAString_SetsTheString
     * @depends getErrorMessage_IsCallable
     */
    public function merge_SetsErrorMessage(): void
    {
        // Setup
        $this->setUpSut();
        $inputToMerge = new ChainValidatedInput(static::AN_INPUT_NAME);
        $inputToMerge->setErrorMessage(static::A_CUSTOM_ERROR_MESSAGE);

        // Execute
        $this->sut->merge($inputToMerge);
        $result = $this->sut->getErrorMessage();

        // Assert
        $this->assertSame(static::A_CUSTOM_ERROR_MESSAGE, $result);
    }

    /**
     * @test
     * @depends merge_IsCallable
     * @depends setName_SetsTheName
     * @depends getName_ReturnsTheNameOfAnInput
     */
    public function merge_SetsName(): void
    {
        // Setup
        $this->setUpSut();
        $inputToMerge = new ChainValidatedInput(static::AN_INPUT_NAME);
        $inputToMerge->setName(static::A_CUSTOM_INPUT_NAME);

        // Execute
        $this->sut->merge($inputToMerge);
        $result = $this->sut->getName();

        // Assert
        $this->assertSame(static::A_CUSTOM_INPUT_NAME, $result);
    }

    /**
     * @test
     * @depends merge_IsCallable
     * @depends isRequired_ReturnsABoolean
     * @depends setRequired_SetsAnInputAsNotRequired
     */
    public function merge_SetsIsRequired(): void
    {
        // Setup
        $this->setUpSut();
        $inputToMerge = new ChainValidatedInput(static::AN_INPUT_NAME);
        $inputToMerge->setRequired(static::NOT_REQUIRED);

        // Execute
        $this->sut->merge($inputToMerge);
        $result = $this->sut->isRequired();

        // Assert
        $this->assertSame(static::NOT_REQUIRED, $result);
    }

    /**
     * @test
     * @depends merge_IsCallable
     * @depends setValue_SetsAValue
     * @depends setFilterChain_SetsAFilterChain
     * @depends getRawValue_ReturnsTheInputValue_WithoutTheFilterChainApplied
     */
    public function merge_SetsValue_ToRawValue_ForInputsThatAreNotLaminas(): void
    {
        // Setup
        $this->setUpSut();
        $this->sut->setValue(static::A_RAW_INPUT_VALUE);
        $inputToMerge = new ChainValidatedInput(static::AN_INPUT_NAME);
        $inputToMerge->setValue(static::A_SECOND_RAW_INPUT_VALUE);
        $inputToMerge->setFilterChain($this->filterChainThatConvertsAllValuesTo(static::A_FILTERED_INPUT_VALUE));

        // Execute
        $this->sut->merge($inputToMerge);
        $result = $this->sut->getRawValue();

        // Assert
        $this->assertSame(static::A_SECOND_RAW_INPUT_VALUE, $result);
    }

    /**
     * @test
     * @depends merge_IsCallable
     * @depends getRawValue_ReturnsTheInputValue_WithoutTheFilterChainApplied
     */
    public function merge_SetsValue_ToRawValue_ForInputsThatAreLaminasAndHaveAValueSet(): void
    {
        // Setup
        $this->setUpSut();
        $this->sut->setValue(static::A_RAW_INPUT_VALUE);
        $inputToMerge = new Input(static::AN_INPUT_NAME);
        $inputToMerge->setValue(static::A_SECOND_RAW_INPUT_VALUE);
        $inputToMerge->setFilterChain($this->filterChainThatConvertsAllValuesTo(static::A_FILTERED_INPUT_VALUE));

        // Execute
        $this->sut->merge($inputToMerge);
        $result = $this->sut->getRawValue();

        // Assert
        $this->assertSame(static::A_SECOND_RAW_INPUT_VALUE, $result);
    }

    /**
     * @test
     * @depends merge_IsCallable
     * @depends getRawValue_ReturnsTheInputValue_WithoutTheFilterChainApplied
     */
    public function merge_DoesNotSetValueForInputsThatAreLaminasDoNotHaveAValueSet(): void
    {
        // Setup
        $this->setUpSut();
        $this->sut->setValue(static::A_RAW_INPUT_VALUE);
        $inputToMerge = new Input(static::AN_INPUT_NAME);
        $inputToMerge->setFilterChain($this->filterChainThatConvertsAllValuesTo(static::A_FILTERED_INPUT_VALUE));

        // Execute
        $this->sut->merge($inputToMerge);
        $result = $this->sut->getRawValue();

        // Assert
        $this->assertSame(static::A_RAW_INPUT_VALUE, $result);
    }

    /**
     * @test
     * @depends getFilterChain_ReturnsAFilterChain
     * @depends setFilterChain_SetsAFilterChain
     * @depends merge_IsCallable
     */
    public function merge_MergesFilterChain(): void
    {
        // Setup
        $this->setUpSut();
        $this->sut->setFilterChain($this->filterChainThatAddsSuffix(static::A_STRING_SUFFIX));
        $inputToMerge = new Input(static::AN_INPUT_NAME);
        $inputToMerge->setFilterChain($this->filterChainThatAddsSuffix(static::A_SECOND_STRING_SUFFIX));

        // Execute
        $this->sut->merge($inputToMerge);
        $result = $this->sut->getFilterChain()->filter(static::AN_EMPTY_RAW_INPUT_VALUE);

        // Assert
        $this->assertEquals(static::A_STRING_SUFFIX . static::A_SECOND_STRING_SUFFIX, $result);
    }

    /**
     * @test
     * @depends setValidatorChain_SetsTheValidatorChain
     * @depends getValidatorChain_ReturnsAValidatorChain
     * @depends merge_IsCallable
     */
    public function merge_MergesValidatorChain(): void
    {
        // Setup
        $this->setUpSut();
        $this->sut->setValidatorChain($this->validatorChainWithValidator(new NotEmpty()));
        $inputToMerge = new Input(static::AN_INPUT_NAME);
        $inputToMerge->setValidatorChain($this->validatorChainWithValidator(new NotEmpty()));

        // Execute
        $this->sut->merge($inputToMerge);
        $result = $this->sut->getValidatorChain()->count();

        // Assert
        $this->assertEquals(2, $result);
    }

    /**
     * @param mixed $name
     */
    protected function setUpSut($name = null)
    {
        $this->sut = new ChainValidatedInput($name ?? static::AN_INPUT_NAME);
    }

    protected function emptyValidatorChain(): ValidatorChain
    {
        return new ValidatorChain();
    }

    protected function getNotEmptyValidatorFromValidatorChain(ValidatorChain $validatorChain): ?NotEmpty
    {
        foreach ($validatorChain->getValidators() as $validatorConfig) {
            if (!is_array($validatorConfig)) {
                continue;
            }
            if (!($validatorConfig['instance'] ?? null) instanceof NotEmpty) {
                continue;
            }
            return $validatorConfig['instance'];
        }

        return null;
    }

    protected function validatorChainWithValidator(ValidatorInterface $validator): ValidatorChain
    {
        $chain = new ValidatorChain();
        $chain->attach($validator);
        return $chain;
    }

    protected function errorMessageObjectThatCastsToAString(string $errorMessage): object
    {
        return new class($errorMessage) {
            private $val;

            public function __construct(string $val)
            {
                $this->val = $val;
            }

            public function __toString()
            {
                return $this->val;
            }
        };
    }

    /**
     * @param mixed $val
     */
    protected function filterChainThatConvertsAllValuesTo($val): FilterChain
    {
        $filterChain = new FilterChain();
        $filterChain->attach(static fn() => $val);
        return $filterChain;
    }

    protected function filterChainThatAddsSuffix(string $suffix): FilterChain
    {
        $filterChain = new FilterChain();
        $filterChain->attach(static fn($val) => ($val ?? '') . $suffix);
        return $filterChain;
    }

    protected function validatorChainWithErrorMessage(): ValidatorChain
    {
        $chain = new ValidatorChain();
        $chain->attach(new NotEmpty());
        $chain->isValid(null);
        return $chain;
    }
}
