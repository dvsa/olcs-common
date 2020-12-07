<?php

namespace CommonTest\InputFilter;

use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;
use Mockery as m;
use Common\InputFilter\Input;

/**
 * Class InputTest
 * @package CommonTest\InputFilter
 */
class InputTest extends TestCase
{
    public function testGetValue()
    {
        $value = 'raw';
        $filtered = 'filtered';

        $sut = new Input();

        $mockFilterChain = m::mock('Laminas\Filter\FilterChain');
        $mockFilterChain->shouldReceive('filter')->once()->with($value)->andReturn($filtered);
        $sut->setFilterChain($mockFilterChain);

        $sut->setValue($value);
        $this->assertEquals($filtered, $sut->getValue());

        //assert only filtered once
        $sut->getValue();
    }

    public function testSetValueResetsFilter()
    {
        $value = 'raw';
        $value2 = 'raw2';
        $filtered = 'filtered';
        $filtered2 = 'filtered2';

        $sut = new Input();

        $mockFilterChain = m::mock('Laminas\Filter\FilterChain');
        $mockFilterChain->shouldReceive('filter')->once()->with($value)->andReturn($filtered);
        $mockFilterChain->shouldReceive('filter')->once()->with($value2)->andReturn($filtered2);
        $sut->setFilterChain($mockFilterChain);

        $sut->setValue($value);
        $this->assertEquals($filtered, $sut->getValue());

        $sut->setValue($value2);
        $this->assertEquals($filtered2, $sut->getValue());
    }
}
