<?php

namespace CommonTest\Service\Qa;

use Common\Service\Qa\DateNotInPastValidator;
use Common\Service\Qa\DateSelect;
use Common\Service\Qa\DateSelectFilter;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\Validator\Date as DateValidator;

/**
 * DateSelectTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class DateSelectTest extends MockeryTestCase
{
    private $dateSelect;

    public function setUp()
    {
        $this->dateSelect = m::mock(DateSelect::class)->makePartial()
            ->shouldAllowMockingProtectedMethods();
    }

    public function testSetValueWithString()
    {
        $value = '2020-05-22';

        $expectedArray = [
            'year' => '2020',
            'month' => '05',
            'day' => '22'
        ];

        $this->dateSelect->shouldReceive('callParentSetValue')
            ->with($expectedArray)
            ->once();

        $this->dateSelect->setValue($value);
    }

    /**
     * @dataProvider dpSetValueWithOther
     */
    public function testSetValueWithOther($value)
    {
        $this->dateSelect->shouldReceive('callParentSetValue')
            ->with($value)
            ->once();

        $this->dateSelect->setValue($value);
    }

    public function dpSetValueWithOther()
    {
        return [
            [
                ['key1' => 'value1', 'key2' => 'value2']
            ],
            [431],
            [true],
        ];
    }

    public function testGetInputSpecification()
    {
        $name = 'foo';

        $expectedSpecification = [
            'name' => $name,
            'required' => false,
            'filters' => [
                [
                    'name' => DateSelectFilter::class
                ]
            ],
            'validators' => [
                [
                    'name' => DateValidator::class,
                    'options' => [
                        'format' => 'Y-m-d',
                        'break_chain_on_failure' => true,
                        'messages' => [
                            DateValidator::INVALID_DATE => 'qanda.date.error.invalid-date'
                        ]
                    ]
                ],
                [
                    'name' => DateNotInPastValidator::class
                ]
            ]
        ];

        $this->dateSelect->setName($name);

        $this->assertEquals(
            $expectedSpecification,
            $this->dateSelect->getInputSpecification()
        );
    }
}