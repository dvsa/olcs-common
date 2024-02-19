<?php

namespace CommonTest\Form\Elements\Custom;

use Common\Form\Elements\Custom\VehiclePlatedWeight;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * @covers \Common\Form\Elements\Custom\VehiclePlatedWeight
 */
class VehiclePlatedWeightTest extends MockeryTestCase
{
    public function testGetInputSpecification()
    {
        /** @var VehiclePlatedWeight $sut */
        $sut = m::mock(VehiclePlatedWeight::class)->makePartial()
            ->shouldReceive('getName')->once()->andReturn('unit_Name')
            ->getMock();

        $actual = $sut->getInputSpecification();

        static::assertEquals('unit_Name', $actual['name']);
        static::assertEquals(
            [
                \Laminas\Validator\Digits::class,
                \Laminas\Validator\Between::class,
            ],
            array_map(
                fn($item) => $item['name'],
                $actual['validators']
            )
        );
    }

    /**
     * @dataProvider dpTestGetInputSpecificationOptions
     */
    public function testGetInputSpecificationOptions($options, $expect)
    {
        /** @var VehiclePlatedWeight $sut */
        $sut = new VehiclePlatedWeight(null, $options);

        $actual = $sut->getInputSpecification();

        foreach ($expect as $key => $val) {
            static::assertEquals($val, $actual[$key]);
        }
    }

    public function dpTestGetInputSpecificationOptions()
    {
        return [
            [
                'options' => [
                    'required' => true,
                    'allow_empty' => true,
                ],
                'expect' => [
                    'required' => true,
                    'allow_empty' => true,
                ]
            ]
        ];
    }
}
