<?php

/**
 * Irhp Permit Range Permit Number Test
 *
 * @author Scott Callaway <scott.callaway@capgemini.com>
 */
namespace CommonTest\Service\Table\Formatter;

use Common\Service\Table\Formatter\IrhpPermitRangePermitNumber;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class IrhpPermitRangePermitNumberTest extends MockeryTestCase
{
    /**
     * Test the format method
     *
     * @group Formatters
     * @group IrhpPermitSectorFormatter
     *
     * @dataProvider formatProvider
     */
    public function testFormat($data, $expected)
    {
        $sm = m::mock(\Laminas\ServiceManager\ServiceLocatorInterface::class);
        $sm->shouldReceive('get->fromRoute')
            ->with(
                'admin-dashboard/admin-permits/ranges',
                [
                    'stockId' => '1',
                    'action' => 'edit',
                    'id' => '1'
                ]
            )
            ->andReturn('WINDOW_EDIT_URL');

        static::assertEquals($expected, IrhpPermitRangePermitNumber::format($data, [], $sm));
    }

    /**
     * Data provider
     *
     * @return array
     */
    public function formatProvider()
    {
        return [
            [
                'data' => [
                    'prefix' => '',
                    'fromNo' => '1',
                    'toNo' => '2',
                    'irhpPermitStock' => [
                        'id' => '1'
                    ],
                    'id' => '1'
                ],
                'expect' => "<a class='strong js-modal-ajax' href='WINDOW_EDIT_URL'>1 to 2</a>",
            ],
            [
                'data' => [
                    'prefix' => 'UK',
                    'fromNo' => '1',
                    'toNo' => '2',
                    'irhpPermitStock' => [
                        'id' => '1'
                    ],
                    'id' => '1'
                ],
                'expect' => "<a class='strong js-modal-ajax' href='WINDOW_EDIT_URL'>UK1 to UK2</a>",
            ],
        ];
    }
}
