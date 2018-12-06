<?php

/**
 * Task date formatter tests
 *
 * @author Nick payne <nick.payne@valtech.co.uk>
 */

namespace CommonTest\Service\Table\Formatter;

use Common\Service\Table\Formatter\TaskDate;

/**
 * Task date formatter tests
 *
 * @author Nick payne <nick.payne@valtech.co.uk>
 */
class TaskDateTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider provider
     */
    public function testFormat($data, $column, $expected)
    {
        $mockTranslator = $this->createPartialMock('\stdClass', array('translate'));

        $sm = $this->createPartialMock('\stdClass', array('get'));
        $sm->expects($this->any())
            ->method('get')
            ->with('translator')
            ->will($this->returnValue($mockTranslator));
        $this->assertEquals($expected, TaskDate::format($data, $column, $sm));
    }

    /**
     * Data provider
     *
     * @return array
     */
    public function provider()
    {
        return [
            'non-urgent' => [
                [
                    'date' => '2013-01-01',
                ],
                [
                    'dateformat' => 'd/m/Y',
                    'name' => 'date',
                ],
                '01/01/2013',
            ],
            'urgent' => [
                [
                    'date' => '2013-01-01',
                    'urgent' => 'Y',
                    'isClosed' => 'N',
                ],
                [
                    'dateformat' => 'd/m/Y',
                    'name' => 'date',
                ],
                '01/01/2013 (urgent)',
            ],
            'closed non-urgent' => [
                [
                    'date' => '2013-01-01',
                    'isClosed' => 'Y',
                    'urgent' => 'N',
                ],
                [
                    'dateformat' => 'd/m/Y',
                    'name' => 'date',
                ],
                '01/01/2013 <span class="status red">closed</span>',
            ],
            'closed urgent' => [
                [
                    'date' => '2013-01-01',
                    'isClosed' => 'Y',
                    'urgent' => 'Y',
                ],
                [
                    'dateformat' => 'd/m/Y',
                    'name' => 'date',
                ],
                '01/01/2013 (urgent) <span class="status red">closed</span>',
            ],
        ];
    }
}
