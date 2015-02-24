<?php

/**
 * Task Owner Formatter Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Table\Formatter;

use PHPUnit_Framework_TestCase;
use Common\Service\Table\Formatter\TaskOwner;

/**
 * Task Owner Formatter Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class TaskOwnerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider providerFormat
     */
    public function testFormat($data, $expected)
    {
        $this->assertEquals($expected, TaskOwner::format($data));
    }

    public function providerFormat()
    {
        return [
            [
                [
                    'teamName' => null,
                    'ownerName' => ' '
                ],
                '(Unassigned)'
            ],
            [
                [
                    'teamName' => 'Footeam',
                    'ownerName' => ' '
                ],
                'Footeam (Unassigned)'
            ],
            [
                [
                    'teamName' => null,
                    'ownerName' => 'Foo'
                ],
                '(Foo)'
            ],
            [
                [
                    'teamName' => 'Foo',
                    'ownerName' => 'Bar'
                ],
                'Foo (Bar)'
            ]
        ];
    }
}
