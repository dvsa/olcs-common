<?php

namespace CommonTest\Service\Qa\Custom\EcmtRemoval;

use Common\Service\Qa\Custom\EcmtRemoval\DateBeforeValidator;
use Common\Service\Qa\Custom\EcmtRemoval\DateSelect;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * DateSelectTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class DateSelectTest extends MockeryTestCase
{
    public function testGetInputSpecification()
    {
        $dateMustBeBefore = '2020-05-02';

        $dateSelect = m::mock(DateSelect::class)->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $parentInputSpecification = [
            'key1' => [
                'foo1' => 'bar1',
                'foo2' => 'bar2',
            ],
            'validators' => [
                [
                    'validator1key1' => 'validator1value1',
                    'validator1key2' => 'validator1value2',
                ],
                [
                    'validator2key1' => 'validator2value1',
                    'validator2key2' => 'validator2value2',
                ]
            ],
            'key3' => [
                'foo1' => 'bar1',
                'foo2' => 'bar2',
            ]
        ];

        $dateSelect->shouldReceive('callParentGetInputSpecification')
            ->andReturn($parentInputSpecification);

        $expectedInputSpecification = [
            'key1' => [
                'foo1' => 'bar1',
                'foo2' => 'bar2',
            ],
            'validators' => [
                [
                    'validator1key1' => 'validator1value1',
                    'validator1key2' => 'validator1value2',
                ],
                [
                    'validator2key1' => 'validator2value1',
                    'validator2key2' => 'validator2value2',
                ],
                [
                    'name' => DateBeforeValidator::class,
                    'options' => [
                        'dateMustBeBefore' => $dateMustBeBefore
                    ]
                ]
            ],
            'key3' => [
                'foo1' => 'bar1',
                'foo2' => 'bar2',
            ]
        ];

        $options = [
            'dateMustBeBefore' => $dateMustBeBefore
        ];

        $dateSelect->setOptions($options);

        $this->assertEquals(
            $expectedInputSpecification,
            $dateSelect->getInputSpecification()
        );
    }
}
