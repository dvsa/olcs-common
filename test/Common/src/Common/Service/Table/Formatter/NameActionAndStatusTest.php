<?php
namespace CommonTest\Service\Table\Formatter;

use Common\Service\Table\Formatter\NameActionAndStatus;

class NameActionAndStatusTest extends \PHPUnit\Framework\TestCase
{
    const TEST_ID = 12345;
    const TEST_TITLE = 'TEST_TITLE';
    const TEST_FORENAME = '';
    const TEST_FAMILY_NAME = '';

    /**
     * @dataProvider provider
     */
    public function testFormat($data, $expected)
    {
        $this->assertEquals($expected, NameActionAndStatus::format($data));
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
                '<input class="" name="table[action][edit][' . self::TEST_ID . ']" value="'
                . self::TEST_FORENAME . ' ' . self::TEST_FAMILY_NAME
                . '" type="submit">'
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
                '<input class="" name="table[action][edit][' . self::TEST_ID . ']" value="'
                . self::TEST_TITLE . ' ' . self::TEST_FORENAME . ' ' . self::TEST_FAMILY_NAME
                . '" type="submit">'
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
                '<input class="" name="table[action][edit][' . self::TEST_ID . ']" value="'
                . self::TEST_TITLE . ' ' . self::TEST_FORENAME . ' ' . self::TEST_FAMILY_NAME
                . '" type="submit"> <span class="overview__status green">New</span>'
            ]
        ];
    }
}
