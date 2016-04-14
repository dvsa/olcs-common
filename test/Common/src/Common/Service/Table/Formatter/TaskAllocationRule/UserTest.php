<?php

namespace CommonTest\Service\Table\Formatter\TaskAllocationRule;

use Common\Service\Table\Formatter\TaskAllocationRule\User;

/**
 * User test
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class UserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test the format method
     *
     * @dataProvider provider
     */
    public function testFormat($expected, $data)
    {
        $sut = new User();

        $this->assertSame($expected, $sut->format($data));
    }

    /**
     * Data provider
     *
     * @return array
     */
    public function provider()
    {
        return [
            // expected, data
            ['Mary Jones', ['forename' => 'Mary', 'familyName' => 'Jones']],
            ['Unassigned', ['forename' => '', 'familyName' => '', 'taskAlphaSplits' => null]],
            ['Unassigned', ['forename' => '', 'familyName' => '', 'taskAlphaSplits' => []]],
            ['[Alpha split]', ['forename' => '', 'familyName' => '', 'taskAlphaSplits' => [1, 2]]],
        ];
    }
}
