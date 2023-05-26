<?php

namespace CommonTest\Service\Table\Formatter;

use Common\Service\Table\Formatter\NameActionAndStatus;

class NameActionAndStatusTest extends \PHPUnit\Framework\TestCase
{
    private const TEST_ID = 12345;
    private const TEST_TITLE = 'TEST_TITLE';
    private const TEST_FORENAME = '';
    private const TEST_FAMILY_NAME = '';

    /**
     * @dataProvider provider
     */
    public function testFormat($data, $expected)
    {
        $this->assertEquals($expected, (new NameActionAndStatus())->format($data));
    }

    public function provider()
    {
        return [
            [
                [
                    'id' => self::TEST_ID,
                    'forename' => self::TEST_FORENAME,
                    'familyName' => self::TEST_FAMILY_NAME,
                    'title' => [
                        'description' => ''
                    ],
                    'status' => null
                ],
                sprintf(
                    NameActionAndStatus::BUTTON_FORMAT,
                    self::TEST_ID,
                    self::TEST_FORENAME . ' ' . self::TEST_FAMILY_NAME
                ),
            ],
            [
                [
                    'id' => self::TEST_ID,
                    'forename' => self::TEST_FORENAME,
                    'familyName' => self::TEST_FAMILY_NAME,
                    'title' => [
                        'description' => self::TEST_TITLE
                    ],
                    'status' => null
                ],
                sprintf(
                    NameActionAndStatus::BUTTON_FORMAT,
                    self::TEST_ID,
                    self::TEST_TITLE . ' ' . self::TEST_FORENAME . ' ' . self::TEST_FAMILY_NAME
                ),
            ],
            [
                [
                    'id' => self::TEST_ID,
                    'forename' => self::TEST_FORENAME,
                    'familyName' => self::TEST_FAMILY_NAME,
                    'title' => [
                        'description' => self::TEST_TITLE
                    ],
                    'status' => 'new'
                ],
                sprintf(
                    NameActionAndStatus::BUTTON_FORMAT,
                    self::TEST_ID,
                    self::TEST_TITLE . ' ' . self::TEST_FORENAME . ' ' . self::TEST_FAMILY_NAME
                ) . ' <span class="overview__status green">New</span>'
            ]
        ];
    }
}
