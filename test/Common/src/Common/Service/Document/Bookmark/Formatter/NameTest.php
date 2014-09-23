<?php

namespace CommonTest\Service\Document\Bookmark\Formatter;

use Common\Service\Document\Bookmark\Formatter\Name;

class NameTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider nameProvider
     */
    public function testFormat($input, $expected)
    {
        $this->assertEquals(
            $expected,
            Name::format($input)
        );
    }

    public function nameProvider()
    {
        return [
            [
                [
                    'forename' => 'Forename',
                    'familyName' => 'Surname'
                ],
                'Forename Surname'
            ]
        ];
    }
}
