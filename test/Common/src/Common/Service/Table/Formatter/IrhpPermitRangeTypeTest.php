<?php

namespace CommonTest\Service\Table\Formatter;

use Common\RefData;
use Common\Service\Table\Formatter\IrhpPermitRangeType;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * IrhpPermitRangeType test
 */
class IrhpPermitRangeTypeTest extends MockeryTestCase
{
    /**
     * @dataProvider dpFormat
     */
    public function testFormat($row, $expectedOutput)
    {
        $column = ['name' => 'typeDescription'];

        $sut = new IrhpPermitRangeType();

        $sm = m::mock(ServiceLocatorInterface::class);
        $sm->allows('get->translate')
            ->andReturnUsing(
                function ($key) {
                    return '_TRNSLT_' . $key;
                }
            );

        $this->assertEquals(
            $expectedOutput,
            $sut->format($row, $column, $sm)
        );
    }

    public function dpFormat()
    {
        return [
            [
                [
                    'irhpPermitStock' => [
                        'irhpPermitType' => [
                            'isBilateral' => false,
                        ]
                    ]
                ],
                'N/A',
            ],
            [
                [
                    'irhpPermitStock' => [
                        'irhpPermitType' => [
                            'isBilateral' => true,
                        ]
                    ],
                    'cabotage' => false,
                    'journey' => ['id' => RefData::JOURNEY_SINGLE],
                ],
                '_TRNSLT_permits.irhp.range.type.standard.single',
            ],
            [
                [
                    'irhpPermitStock' => [
                        'irhpPermitType' => [
                            'isBilateral' => true,
                        ]
                    ],
                    'cabotage' => false,
                    'journey' => ['id' => RefData::JOURNEY_MULTIPLE],
                ],
                '_TRNSLT_permits.irhp.range.type.standard.multiple',
            ],
            [
                [
                    'irhpPermitStock' => [
                        'irhpPermitType' => [
                            'isBilateral' => true,
                        ]
                    ],
                    'cabotage' => true,
                    'journey' => ['id' => RefData::JOURNEY_SINGLE],
                ],
                '_TRNSLT_permits.irhp.range.type.cabotage.single',
            ],
            [
                [
                    'irhpPermitStock' => [
                        'irhpPermitType' => [
                            'isBilateral' => true,
                        ]
                    ],
                    'cabotage' => true,
                    'journey' => ['id' => RefData::JOURNEY_MULTIPLE],
                ],
                '_TRNSLT_permits.irhp.range.type.cabotage.multiple',
            ],
            [
                [
                    'irhpPermitStock' => [
                        'irhpPermitType' => [
                            'isBilateral' => true,
                        ],
                        'permitCategory' => [
                            'description' => 'category',
                        ],
                    ],
                ],
                '_TRNSLT_category',
            ],
        ];
    }
}
