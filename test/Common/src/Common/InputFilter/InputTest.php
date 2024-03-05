<?php

namespace CommonTest\InputFilter;

use Common\InputFilter\Input;
use Laminas\Filter\FilterChain;
use Common\Test\MockeryTestCase;

/**
 * @see Input
 */
class InputTest extends MockeryTestCase
{
    protected const AN_INPUT_NAME = 'AN INPUT NAME';
    protected const A_RAW_INPUT_VALUE = 'A RAW INPUT VALUE';
    protected const A_SECOND_RAW_INPUT_VALUE = 'A SECOND RAW INPUT VALUE';
    protected const A_FILTERED_INPUT_VALUE = 'A FILTERED INPUT VALUE';
    protected const A_SECOND_FILTERED_INPUT_VALUE = 'A SECOND FILTERED INPUT VALUE';

    /**
     * @var Input
     */
    protected $sut;

    /**
     * @test
     */
    public function setValue_IsCallable()
    {
        // Setup
        $this->setUpSut();

        // Assert
        $this->assertIsCallable([$this->sut, 'setValue']);
    }

    /**
     * @test
     */
    public function getValue_IsCallable()
    {
        // Setup
        $this->setUpSut();

        // Assert
        $this->assertIsCallable([$this->sut, 'getValue']);
    }

    /**
     * @test
     * @depends getValue_IsCallable
     */
    public function getValue_FiltersValue()
    {
        // Setup
        $this->setUpSut();
        $this->sut->setFilterChain($this->aFilterChainThatReturns(static::A_FILTERED_INPUT_VALUE));
        $this->sut->setValue(static::A_RAW_INPUT_VALUE);

        // Execute
        $result = $this->sut->getValue();

        // Assert
        $this->assertSame(self::A_FILTERED_INPUT_VALUE, $result);
    }

    /**
     * @test
     * @depends getValue_FiltersValue
     */
    public function getValue_FiltersValue_OnceWhenTheValueHasNotBeenSetAgain()
    {
        // Setup
        $this->setUpSut();
        $this->sut->setValue(static::A_RAW_INPUT_VALUE);

        // Execute
        $this->sut->getValue();
        $this->sut->setFilterChain($this->aFilterChainThatReturns(static::A_FILTERED_INPUT_VALUE));
        $result = $this->sut->getValue();

        // Assert
        $this->assertSame(static::A_RAW_INPUT_VALUE, $result);
    }

    /**
     * @test
     * @depends getValue_IsCallable
     * @depends setValue_IsCallable
     */
    public function getValue_FiltersValue_TwiceWhenTheValueHasBeenSetSinceFirstBeingGotten()
    {
        // Setup
        $this->setUpSut();

        // Execute
        $this->sut->setValue(static::A_RAW_INPUT_VALUE);
        $this->sut->getValue();
        $this->sut->setFilterChain($this->aFilterChainThatReturns(static::A_FILTERED_INPUT_VALUE));
        $this->sut->setValue(static::A_RAW_INPUT_VALUE);
        $result = $this->sut->getValue();

        // Assert
        $this->assertSame(static::A_FILTERED_INPUT_VALUE, $result);
    }

    /**
     * @param mixed $name
     */
    protected function setUpSut($name = self::AN_INPUT_NAME)
    {
        $this->sut = new Input($name);
    }

    /**
     * @param $value
     * @return FilterChain
     */
    protected function aFilterChainThatReturns($value): FilterChain
    {
        $chain = new FilterChain();
        $chain->attach(fn() => $value);
        return $chain;
    }
}
