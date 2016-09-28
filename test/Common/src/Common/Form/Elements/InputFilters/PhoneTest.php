<?php

namespace CommonTest\Form\Elements\InputFilters;

use Common\Form\Elements\InputFilters\Phone;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;

/**
 * @covers Common\Form\Elements\InputFilters\Phone
 */
class PhoneTest extends MockeryTestCase
{
    public function testValidators()
    {
        /** @var Phone $sut */
        $sut = m::mock(Phone::class)->makePartial()
            ->shouldReceive('getName')->once()->andReturn('unit_Name')
            ->getMock();

        $actual = $sut->getInputSpecification();

        static::assertEquals('unit_Name', $actual['name']);
        static::assertEquals(
            [
                \Zend\Validator\Regex::class,
                \Zend\Validator\StringLength::class,
            ],
            array_map(
                function ($item) {
                    return $item['name'];
                },
                $actual['validators']
            )
        );
    }
}
