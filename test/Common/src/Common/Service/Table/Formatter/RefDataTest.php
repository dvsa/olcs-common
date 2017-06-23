<?php

namespace CommonTest\Service\Table\Formatter;

use Common\Service\Table\Formatter\RefData;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * @covers \Common\Service\Table\Formatter\RefData
 */
class RefDataTest extends MockeryTestCase
{
    /**
     * @dataProvider dpTestFormat
     */
    public function testFormat($data, $expect)
    {
        $mockSm = m::mock(\Zend\ServiceManager\ServiceLocatorInterface::class);
        $mockSm
            ->shouldReceive('get->translate')
            ->andReturnUsing(
                function ($text) {
                    return '_TRNSLT_' . $text;
                }
            );

        $col = [
            'name' => 'statusField',
            'formatter' => 'RefData',
            'separator' => '@unit_Sepr@',
        ];

        static::assertEquals($expect, RefData::format($data, $col, $mockSm));
    }

    public function dpTestFormat()
    {
        return [
            'noField' => [
                'data' => [
                    'statusField' => [],
                    'unit_field' => 'unit_val',
                ],
                'expect' => '',
            ],
            'simple' => [
                'data' => [
                    'statusField' => [
                        'id' => 'unit_id',
                        'description' => 'unit_Desc',
                    ],

                ],
                'expect' => '_TRNSLT_unit_Desc',
            ],
            'multi' => [
                'data' => [
                    'statusField' => [
                        [
                            'id' => 'unit_Id1',
                            'description' => 'unit_Desc1',
                        ],
                        [
                            'id' => 'unit_Id2',
                            'description' => 'unit_Desc2',
                        ],
                    ],

                ],
                'expect' => '_TRNSLT_unit_Desc1@unit_Sepr@_TRNSLT_unit_Desc2',
            ],
        ];
    }
}
