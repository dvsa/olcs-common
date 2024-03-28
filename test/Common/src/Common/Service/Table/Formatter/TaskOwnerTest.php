<?php

/**
 * Task Owner Formatter Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace CommonTest\Service\Table\Formatter;

use Common\Service\Table\Formatter\TaskOwner;

/**
 * Task Owner Formatter Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class TaskOwnerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider providerFormat
     */
    public function testFormat($data, $expected): void
    {
        $this->assertEquals($expected, (new TaskOwner())->format($data));
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
            ],
            [
                [
                    'teamName' => 'Footeam',
                    'ownerName' => ','
                ],
                'Footeam (Unassigned)'
            ],
        ];
    }
}
