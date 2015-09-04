<?php

/**
 * Transaction Amount Sum formatter test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace CommonTest\Service\Table\Formatter;

use Common\RefData;
use Common\Service\Table\Formatter\TransactionAmountSum;

/**
 * Transaction Amount Sum formatter test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class TransactionAmountSumTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Test the format method
     *
     * @group Formatters
     *
     * @dataProvider provider
     */
    public function testFormat($data, $expected)
    {
        $column = ['name' => 'amount'];
        $this->assertSame($expected, TransactionAmountSum::format($data, $column));
    }

    /**
     * Data provider
     *
     * @return array
     */
    public function provider()
    {
        return [
            'no transactions' => [
                [],
                '£0.00'
            ],
            'invalid amounts' => [
                [
                    [
                        'transaction' => [
                            'status' => [
                                'id' => RefData::TRANSACTION_STATUS_COMPLETE
                            ]
                        ],
                        'amount' => 'A'
                    ],
                    [
                        'transaction' => [
                            'status' => [
                                'id' => RefData::TRANSACTION_STATUS_COMPLETE
                            ]
                        ],
                        'amount' => 'B'
                    ],
                ],
                '£0.00'
            ],
            'one complete transaction' => [
                [
                    [
                        'transaction' => [
                            'status' => [
                                'id' => RefData::TRANSACTION_STATUS_COMPLETE
                            ]
                        ],
                        'amount' => 5
                    ],
                ],
                '£5.00'
            ],
            'two complete transactions' => [
                [
                    [
                        'transaction' => [
                            'status' => [
                                'id' => RefData::TRANSACTION_STATUS_COMPLETE
                            ]
                        ],
                        'amount' => 5
                    ],
                    [
                        'transaction' => [
                            'status' => [
                                'id' => RefData::TRANSACTION_STATUS_COMPLETE
                            ]
                        ],
                        'amount' => 7
                    ],
                ],
                '£12.00'
            ],
            'two complete one invalid' => [
                [
                    [
                        'transaction' => [
                            'status' => [
                                'id' => RefData::TRANSACTION_STATUS_COMPLETE
                            ]
                        ],
                        'amount' => 5
                    ],
                    [
                        'transaction' => [
                            'status' => [
                                'id' => RefData::TRANSACTION_STATUS_COMPLETE
                            ]
                        ],
                        'amount' => 7
                    ],
                    [
                        'transaction' => [
                            'status' => [
                                'id' => RefData::TRANSACTION_STATUS_COMPLETE
                            ]
                        ],
                        'amount' => 'A'
                    ]
                ],
                '£12.00'
            ],
            'one outstanding two complete' => [
                [
                    [
                        'transaction' => [
                            'status' => [
                                'id' => RefData::TRANSACTION_STATUS_OUTSTANDING
                            ]
                        ],
                        'amount' => 5
                    ],
                    [
                        'transaction' => [
                            'status' => [
                                'id' => RefData::TRANSACTION_STATUS_COMPLETE
                            ]
                        ],
                        'amount' => 7
                    ],
                    [
                        'transaction' => [
                            'status' => [
                                'id' => RefData::TRANSACTION_STATUS_COMPLETE
                            ]
                        ],
                        'amount' => 95
                    ]
                ],
                '£102.00'
            ],
        ];
    }
}
