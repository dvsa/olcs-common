<?php

/**
 * Printer exception formatter test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace CommonTest\Service\Table\Formatter;

use Common\Service\Table\Formatter\PrinterException;
use PHPUnit_Framework_TestCase;

/**
 * Printer exception formatter test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class PrinterExceptionTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test the format method
     *
     * @dataProvider provider
     */
    public function testFormat($data, $expected)
    {
        $this->assertEquals($expected, PrinterException::format($data));
    }

    /**
     * Data provider
     *
     * @return array
     */
    public function provider()
    {
        return [
            'team' => [
                [
                    'team' => [
                        'name' => 'foo',
                    ],
                    'user' => null
                ],
                'foo',
            ],
            'userWithName' => [
                [
                    'user' => [
                        'contactDetails' => [
                            'person' => [
                                'forename' => 'foo',
                                'familyName' => 'bar'
                            ]
                        ]
                    ],
                ],
                'foo bar',
            ],
            'userWithLoginId' => [
                [
                    'user' => [
                        'loginId' => 'foo'
                    ],
                ],
                'foo',
            ]
        ];
    }
}