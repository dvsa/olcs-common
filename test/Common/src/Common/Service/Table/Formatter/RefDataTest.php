<?php

namespace CommonTest\Service\Table\Formatter;

use Common\Service\Table\Formatter\RefData;
use Dvsa\Olcs\Utils\Translation\TranslatorDelegator;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * @covers \Common\Service\Table\Formatter\RefData
 */
class RefDataTest extends MockeryTestCase
{
    protected $translator;
    protected $sut;

    protected function setUp(): void
    {
        $this->translator = m::mock(TranslatorDelegator::class);
        $this->sut = new RefData($this->translator);
    }

    /**
     * @dataProvider dpTestFormat
     */
    public function testFormat($data, $expect)
    {
        $this->translator
            ->shouldReceive('translate')
            ->andReturnUsing(
                fn($text) => '_TRNSLT_' . $text
            );

        $col = [
            'name' => 'statusField',
            'formatter' => RefData::class,
            'separator' => '@unit_Sepr@',
        ];

        static::assertEquals($expect, $this->sut->format($data, $col));
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
