<?php

namespace CommonTest\Data\Mapper\Continuation;

use Common\Data\Mapper\Continuation\Finances;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * @covers \Common\Data\Mapper\Continuation\Finances
 */
class FinancesTest extends MockeryTestCase
{
    /**
     * @var Finances
     */
    private $sut;

    public function setUp()
    {
        $this->sut = new Finances();
    }

    public function testMapFromResult()
    {
        $data = [
            'version' => 99,
            'averageBalanceAmount' => '123.45',
            'hasOverdraft' => 'Y',
            'overdraftAmount' => '234.56',
            'hasOtherFinances' => 'N',
            'otherFinancesAmount' => '345.67',
            'otherFinancesDetails' => 'FOO',
        ];

        $expected = [
            'finances' => [
                'version' => 99,
                'averageBalance' => '123.45',
                'overdraftFacility' => [
                    'yesNo' => 'Y',
                    'yesContent' => '234.56'
                ],
                'otherFinances' => [
                    'yesNo' => 'N',
                    'yesContent' => [
                        'amount' => '345.67',
                        'detail' => 'FOO'
                    ]
                ]
            ]
        ];

        $this->assertSame($expected, $this->sut->mapFromResult($data));
    }

    public function testMapFromResultDefaults()
    {
        $data = [
            'version' => 99,
            'averageBalanceAmount' => '123.45',
            'hasOverdraft' => 'Y',
            'hasOtherFinances' => 'N',
        ];

        $expected = [
            'finances' => [
                'version' => 99,
                'averageBalance' => '123.45',
                'overdraftFacility' => [
                    'yesNo' => 'Y',
                    'yesContent' => ''
                ],
                'otherFinances' => [
                    'yesNo' => 'N',
                    'yesContent' => [
                        'amount' => '',
                        'detail' => ''
                    ]
                ]
            ]
        ];

        $this->assertSame($expected, $this->sut->mapFromResult($data));
    }

    public function testMapFromForm()
    {
        $formData = [
            'finances' => [
                'version' => 99,
                'averageBalance' => '123.45',
                'overdraftFacility' => [
                    'yesNo' => 'Y',
                    'yesContent' => '234.56'
                ],
                'otherFinances' => [
                    'yesNo' => 'Y',
                    'yesContent' => [
                        'amount' => '345.67',
                        'detail' => 'FOO'
                    ]
                ]
            ]
        ];

        $expected = [
            'version' => 99,
            'averageBalanceAmount' => '123.45',
            'hasOverdraft' => 'Y',
            'overdraftAmount' => '234.56',
            'hasOtherFinances' => 'Y',
            'otherFinancesAmount' => '345.67',
            'otherFinancesDetails' => 'FOO',
        ];

        $this->assertSame($expected, $this->sut->mapFromForm($formData));
    }

    public function testMapFromFormNo()
    {
        $formData = [
            'finances' => [
                'version' => 99,
                'averageBalance' => '123.45',
                'overdraftFacility' => [
                    'yesNo' => 'N',
                    'yesContent' => '234.56'
                ],
                'otherFinances' => [
                    'yesNo' => 'N',
                    'yesContent' => [
                        'amount' => '345.67',
                        'detail' => 'FOO'
                    ]
                ]
            ]
        ];

        $expected = [
            'version' => 99,
            'averageBalanceAmount' => '123.45',
            'hasOverdraft' => 'N',
            'overdraftAmount' => null,
            'hasOtherFinances' => 'N',
            'otherFinancesAmount' => null,
            'otherFinancesDetails' => null,
        ];

        $this->assertSame($expected, $this->sut->mapFromForm($formData));
    }
}
